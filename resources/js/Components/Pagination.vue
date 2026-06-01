<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    links: Array,
});
</script>

<template>
    <div
        v-if="links.length > 3"
        class="flex flex-wrap justify-center items-center mt-8 gap-2"
    >
        <template v-for="(link, key) in links" :key="key">
            <div
                v-if="link.url === null"
                class="px-4 py-2 text-sm leading-4 text-gray-400 border rounded-lg"
                v-html="link.label"
            />
            <Link
                v-else
                class="px-4 py-2 text-sm leading-4 border rounded-lg hover:bg-white focus:border-blue-500 focus:text-blue-500 transition-all"
                :class="{
                    'bg-[#01AAEB] text-white hover:bg-[#0198d1]': link.active,
                }"
                :href="link.url"
                v-html="link.label"
            />
        </template>
    </div>
</template>
