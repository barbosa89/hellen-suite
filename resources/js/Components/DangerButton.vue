<template>
    <button
        class="border-danger-600 bg-danger-600 hover:border-danger-700 hover:bg-danger-700 focus-visible:ring-danger-500 active:bg-danger-800 inline-flex min-h-11 items-center justify-center gap-2 rounded-lg border px-4 py-2 text-sm font-semibold text-white shadow-sm transition-[background-color,border-color,box-shadow] duration-150 ease-out focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-hidden disabled:cursor-not-allowed disabled:opacity-50 motion-reduce:transition-none dark:focus-visible:ring-offset-neutral-950"
    >
        <slot />
    </button>
</template>
