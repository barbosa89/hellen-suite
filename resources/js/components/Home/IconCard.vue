<template>
    <div :class="['card', 'text-white', bg, 'o-hidden', 'h-100']">
        <div class="card-body">
            <div class="card-body-icon">
                <em :class="['fa', 'fa-fw', icon]"></em>
            </div>
            <div v-if="content.length != 0" class="mr-5">
                {{ content }}!
            </div>
            <div v-else class="mr-5">
                <em class="fas fa-spinner fa-pulse"></em>
            </div>
        </div>
        <a class="card-footer text-white clearfix small z-1" :href="url">
            <span class="float-left">{{ $t('common.view.details') }}</span>
            <span class="float-right">
                <i class="fa fa-angle-right"></i>
            </span>
        </a>
    </div>
</template>

<script>
    export default {
        props: {
            bg: {
                type: String,
                default: function () {
                    return 'bg-info'
                }
            },
            icon: {
                type: String,
                default: function () {
                    return 'fa-star'
                }
            },
            content: {
                type: String,
                default: function () {
                    return ''
                }
            },
            url: {
                type: String,
                default: function () {
                    return '/'
                }
            }
        }
    }
</script>
