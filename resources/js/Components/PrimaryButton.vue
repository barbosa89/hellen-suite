<template>
    <button
        class="border-primary-800 bg-primary-800 hover:border-primary-900 hover:bg-primary-900 focus-visible:ring-primary-500 active:border-primary-900 active:bg-primary-900 dark:border-primary-400 dark:bg-primary-400 dark:hover:border-primary-300 dark:hover:bg-primary-300 dark:active:border-primary-300 dark:active:bg-primary-300 inline-flex min-h-11 items-center justify-center gap-2 rounded-lg border px-4 py-2 text-sm font-semibold text-white shadow-sm transition-[background-color,border-color,color,box-shadow] duration-150 ease-out focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-hidden disabled:cursor-not-allowed disabled:opacity-50 motion-reduce:transition-none dark:text-neutral-950 dark:focus-visible:ring-offset-neutral-950"
    >
        <slot />
    </button>
</template>
