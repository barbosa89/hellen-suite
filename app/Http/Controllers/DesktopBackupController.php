<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Backup\CreateBackup;
use App\Actions\Backup\RestoreBackup;
use App\Actions\Backup\SelectBackup;
use App\Exceptions\RuntimeException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

use function is_array;
use function is_string;

class DesktopBackupController extends Controller
{
    public function edit(Request $request): Response
    {
        return Inertia::render('Settings/Backup', [
            'selectedBackup' => $request->session()->get('backup.selected'),
            'pendingExport' => $request->session()->get('backup.export_destination'),
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
            ],
        ]);
    }

    public function store(Request $request, CreateBackup $backup): RedirectResponse
    {
        $destination = $backup->selectDestination();

        if ($destination === null) {
            return redirect()->route('settings.backup.edit')
                ->with('error', trans('backup.messages.export_cancelled'));
        }

        if ($backup->requiresConfirmation($destination, $request->boolean('overwrite'))) {
            $request->session()->put('backup.export_destination', $destination);

            return redirect()->route('settings.backup.edit')
                ->with('error', trans('backup.messages.export_exists', ['path' => $destination]));
        }

        $request->session()->forget('backup.export_destination');

        try {
            $backup->execute($destination);
        } catch (RuntimeException $exception) {
            return redirect()->route('settings.backup.edit')
                ->with('error', trans('backup.messages.export_failed', ['reason' => $exception->getMessage()]));
        }

        return redirect()->route('settings.backup.edit')
            ->with('success', trans('backup.messages.export_completed', ['path' => $destination]));
    }

    public function select(Request $request, SelectBackup $backup): RedirectResponse
    {
        $selected = $backup->selectFile();

        if ($selected === null) {
            return redirect()->route('settings.backup.edit')
                ->with('error', trans('backup.messages.restore_cancelled'));
        }

        try {
            $preview = $backup->execute($selected);
        } catch (RuntimeException $exception) {
            return redirect()->route('settings.backup.edit')
                ->with('error', trans('backup.messages.restore_invalid', ['reason' => $exception->getMessage()]));
        }

        $request->session()->put('backup.selected', $preview);

        return redirect()->route('settings.backup.edit');
    }

    public function confirm(Request $request, RestoreBackup $restore): RedirectResponse
    {
        $selected = $request->session()->get('backup.selected');

        if (! is_array($selected) || ! is_string($selected['path'] ?? null) || $selected['path'] === '') {
            return redirect()->route('settings.backup.edit')
                ->with('error', trans('backup.messages.restore_missing'));
        }

        $request->session()->forget('backup.selected');

        try {
            $restore->execute($selected['path']);
        } catch (RuntimeException $exception) {
            return redirect()->route('settings.backup.edit')
                ->with('error', trans('backup.messages.restore_failed', ['reason' => $exception->getMessage()]));
        }

        return redirect()->route('settings.backup.edit')
            ->with('success', trans('backup.messages.restore_completed'));
    }

    public function cancel(Request $request): RedirectResponse
    {
        $request->session()->forget('backup.selected');

        return redirect()->route('settings.backup.edit');
    }
}
