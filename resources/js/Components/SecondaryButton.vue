<script setup>
defineProps({
    type: {
        type: String,
        default: "button",
    },
});
</script>

<template>
    <button :type="type" class="btn">
        <slot />
    </button>
</template>
