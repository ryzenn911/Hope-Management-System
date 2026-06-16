<script setup>
import { Link, usePage, router } from "@inertiajs/vue3";
import { computed, ref } from "vue";

const page = usePage();

const isDropdownOpen = ref(false);
const user = computed(() => page.props.auth.user);
const notifications = computed(() => page.props.auth.notifications);
const isNotiOpen = ref(false);

const markAllAsRead = () => {
    router.post(
        route("notifications.markRead"),
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                isNotiOpen.value = false;
            },
        },
    );
};
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <nav class="border-b border-gray-100 bg-white"></nav>

        <!-- Page Heading -->
        <header class="bg-white shadow">
            <div class="flex justify-between items-center mx-auto max-w-6xl xl:px-0 px-2">
                <div class="flex items-center justify-center">
                    <img
                        src="/images/logo.png"
                        alt="Logo"
                        class="w-40 object-contain"
                    />
                </div>
                <div class="flex items-center justify-center">
                    <div class="relative mr-4">
                        <button
                            @click="isNotiOpen = !isNotiOpen"
                            class="relative p-2 text-gray-600 hover:bg-gray-100 rounded-full transition-all"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="30"
                                height="30"
                                viewBox="0 0 24 24"
                            >
                                <rect width="24" height="24" fill="none" />
                                <path
                                    fill="currentColor"
                                    d="M21 19v1H3v-1l2-2v-6c0-3.1 2.03-5.83 5-6.71V4a2 2 0 0 1 2-2a2 2 0 0 1 2 2v.29c2.97.88 5 3.61 5 6.71v6zm-7 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2"
                                />
                            </svg>
                            <span
                                v-if="notifications.length > 0"
                                class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] text-white"
                            >
                                {{ notifications.length }}
                            </span>
                        </button>

                        <div
                            v-if="isNotiOpen"
                            @click.outside="isNotiOpen = false"
                            class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-gray-100 z-50 overflow-hidden"
                        >
                            <div
                                class="p-3 border-b flex justify-between items-center bg-gray-50"
                            >
                                <span class="font-bold text-sm text-gray-700"
                                    >Notifications</span
                                >
                                <button
                                    v-if="notifications.length > 0"
                                    @click="markAllAsRead"
                                    class="text-[11px] text-blue-600 hover:underline"
                                >
                                    Mark all as read
                                </button>
                            </div>

                            <div class="max-h-96 overflow-y-auto">
                                <div
                                    v-if="notifications.length === 0"
                                    class="p-8 text-center text-gray-400"
                                >
                                    <p class="text-sm">No new notifications</p>
                                </div>

                                <div
                                    v-if="notifications && notifications.length"
                                    v-for="noti in notifications"
                                    :key="noti.id"
                                    class="p-3 border-b hover:bg-blue-50 transition-colors cursor-pointer"
                                >
                                    <p class="text-sm font-bold text-gray-800">
                                        {{ noti.data?.title }}
                                    </p>
                                    <p class="text-xs text-gray-600">
                                        {{ noti.data?.message }}
                                    </p>
                                    <p
                                        class="text-[10px] text-gray-400 mt-1 italic text-right"
                                    >
                                        Just now
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="relative">
                        <button
                            @click="isDropdownOpen = !isDropdownOpen"
                            class="w-10 h-10 md:w-12 md:h-12 rounded-full bg-[#8BC34A] text-white flex items-center justify-center font-bold shadow-sm focus:outline-none active:scale-90 transition-all"
                        >
                            {{ $page.props.auth.user.first_letter }}
                        </button>

                        <div
                            v-if="isDropdownOpen"
                            @click.away="isDropdownOpen = false"
                            class="absolute right-0 mt-2 w-32 bg-white rounded-xl shadow-lg border border-gray-100 z-50 py-2"
                        >
                            <Link
                                href="/profile"
                                class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-50"
                            >
                                <i
                                    class="fas fa-user-circle mr-3 text-gray-400"
                                ></i>
                                Account
                            </Link>

                            <div class="border-t border-gray-50 my-1"></div>

                            <Link
                                href="/logout"
                                method="post"
                                as="button"
                                class="w-full flex items-center px-3 py-2 text-sm text-red-600 hover:bg-red-50 text-left"
                            >
                                <i class="fas fa-sign-out-alt mr-3"></i>
                                Logout
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main>
            <slot />
        </main>
    </div>
</template>
