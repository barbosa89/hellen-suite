<template>
    <div class="dropdown">
        <button class="btn btn-link" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <em class="fa fa-ellipsis-v"></em>
        </button>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
            <slot></slot>
        </div>
    </div>
</template>
