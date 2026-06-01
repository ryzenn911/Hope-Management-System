<script setup>
import { Link, usePage, router } from "@inertiajs/vue3";
import { computed, ref } from "vue";

const page = usePage();
const user = computed(() => page.props.auth.user);
const isDropdownOpen = ref(false);

const isSidebarOpen = ref(false);

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

const Icons = {
    Search: `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>`,
    Dashboard: `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>`,
    Staff: `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>`,
    Department: `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9h18v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9Z"/><path d="m3 9 2.45-4.9A2 2 0 0 1 7.24 3h9.52a2 2 0 0 1 1.8 1.1L21 9"/></svg>`,
    Leave: `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>`,
    Attendance: `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/><path d="m9 16 2 2 4-4"/></svg>`,
    Report: `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="20" x2="12" y2="10"/><line x1="18" y1="20" x2="18" y2="4"/><line x1="6" y1="20" x2="6" y2="16"/></svg>`,
    Logout: `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>`,
};
</script>

<template>
    <div class="h-screen bg-gray-100 flex">
        <div
            v-if="isSidebarOpen"
            @click="isSidebarOpen = false"
            class="fixed inset-0 bg-black/40 z-30 lg:hidden transition-opacity duration-300"
        ></div>
        <aside
            :class="[
                'lg:translate-x-0',
                isSidebarOpen ? 'translate-x-0' : '-translate-x-full',
                'md:w-72 w-56 bg-white border-r border-gray-100 flex flex-col fixed h-full z-40 shadow-sm transition-transform duration-300 ease-in-out lg:z-20',
            ]"
        >
            <div
                class="flex items-center justify-center gap-1 p-2 border-b border-gray-200 bg-white md:h-28 h-20"
            >
                <img
                    src="/images/logo.png"
                    alt="Logo"
                    class="md:w-40 w-32 object-contain"
                />
            </div>

            <nav class="flex-1 md:px-6 px-2 space-y-2 mt-4 font-siemreap">
                <Link
                    :href="route('admin.dashboard')"
                    :class="
                        route().current('admin.dashboard')
                            ? 'bg-[#01AAEB] text-white shadow-lg shadow-blue-100'
                            : 'text-gray-500 hover:bg-[#01AAEB] hover:text-white'
                    "
                    class="flex items-center px-4 py-3 rounded-xl font-bold transition-all duration-300 font-poppins md:text-base text-sm"
                >
                    <span class="mr-3" v-html="Icons.Dashboard"></span>
                    Dashboard
                </Link>
                <Link
                    :href="route('employees.index')"
                    :class="
                        route().current('employees.*') ||
                        route().current('edit.*') ||
                        route().current('show.*')
                            ? 'bg-[#01AAEB] text-white shadow-lg shadow-blue-100'
                            : 'text-gray-500 hover:bg-[#01AAEB] hover:text-white'
                    "
                    class="flex items-center px-4 py-3 rounded-xl font-bold transition-all duration-300 font-poppins md:text-base text-sm"
                >
                    <span class="mr-3" v-html="Icons.Staff"></span>
                    Staff Management
                </Link>
                <!-- <Link
                    :href="route('admin.attendance')"
                    :class="
                        route().current('admin.attendance')
                            ? 'bg-[#01AAEB] text-white shadow-lg shadow-blue-100'
                            : 'text-gray-500 hover:bg-[#01AAEB] hover:text-white'
                    "
                    class="flex items-center px-4 py-3 rounded-xl font-bold transition-all duration-300 font-poppins"
                >
                    <span class="mr-3" v-html="Icons.Attendance"></span>
                    Attendance
                </Link> -->
                <Link
                    :href="route('admin.leaves.index')"
                    :class="
                        route().current('admin.leaves.*')
                            ? 'bg-[#01AAEB] text-white shadow-lg shadow-blue-100'
                            : 'text-gray-500 hover:bg-[#01AAEB] hover:text-white'
                    "
                     class="flex items-center px-4 py-3 rounded-xl font-bold transition-all duration-300 font-poppins md:text-base text-sm"
                >
                    <span class="mr-3" v-html="Icons.Leave"></span>
                    Leaves
                </Link>
            </nav>

            <div class="p-6 border-t border-gray-200">
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="w-full flex items-center justify-center px-4 py-3 bg-red-500 font-poppins text-white text-sm hover:bg-red-400 rounded-xl transition-all"
                >
                    <span class="mr-3" v-html="Icons.Logout"></span>
                    Logout
                </Link>
            </div>
        </aside>
        <div class="flex-1 flex flex-col overflow-hidden">
            <header
                class="bg-white shadow-sm md:h-28 h-20 flex items-center justify-between px-8"
            >
                <div class="flex items-center gap-3">
                    <button
                        @click="isSidebarOpen = true"
                        class="p-2 rounded-lg text-gray-600 hover:bg-gray-100 lg:hidden focus:outline-none"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <line x1="4" x2="20" y1="12" y2="12" />
                            <line x1="4" x2="20" y1="6" y2="6" />
                            <line x1="4" x2="20" y1="18" y2="18" />
                        </svg>
                    </button>

                    <h2
                        class="text-lg md:text-xl font-bold text-gray-700 truncate max-w-[200px] sm:max-w-none"
                    >
                        <slot name="header" />
                    </h2>
                </div>

                <div class="flex justify-center items-center">
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

                        <!-- Dropdown បញ្ជីសារ -->
                        <div
                            v-if="isNotiOpen"
                            @click.outside="isNotiOpen = false"
                            class="absolute md:right-0 -right-10 mt-2 w-80 bg-white rounded-xl shadow-xl border border-gray-100 z-50 overflow-hidden"
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
                                <!-- បើគ្មានសារ -->
                                <div
                                    v-if="notifications.length === 0"
                                    class="p-8 text-center text-gray-400"
                                >
                                    <p class="text-sm">No new notifications</p>
                                </div>

                                <!-- បញ្ជីសារនីមួយៗ -->
                                <div
                                    v-for="noti in notifications"
                                    :key="noti.id"
                                    class="p-3 border-b hover:bg-blue-50 transition-colors cursor-pointer"
                                >
                                    <p
                                        class="text-sm font-bold text-gray-800 uppercase"
                                    >
                                        {{ noti.data.title }}
                                    </p>
                                    <p class="text-xs text-gray-600">
                                        {{ noti.data.message }}
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
                        <!-- Circle Button -->
                        <button
                            @click="isDropdownOpen = !isDropdownOpen"
                            class="w-10 h-10 md:w-12 md:h-12 rounded-full bg-[#8BC34A] text-white flex items-center justify-center font-bold shadow-sm focus:outline-none active:scale-90 transition-all"
                        >
                            {{
                                $page.props.auth.user.username
                                    .charAt(0)
                                    .toUpperCase()
                            }}
                        </button>

                        <!-- Dropdown Menu -->
                        <div
                            v-if="isDropdownOpen"
                            @click.away="isDropdownOpen = false"
                            class="absolute right-0 mt-2 w-24 bg-white rounded-xl shadow-lg border border-gray-100 z-50 py-2"
                        >
                            <!-- Account Item -->
                            <Link
                                href="/profile"
                                class="flex items-center p-1 text-sm text-gray-700 hover:bg-gray-50"
                            >
                                <i
                                    class="fas fa-user-circle mr-3 text-gray-400"
                                ></i>
                                Account
                            </Link>
                        </div>
                    </div>
                </div>
            </header>

            <main
                class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-5 md:ml-[270px] sm:ml-[100px] ml-0 transition-all duration-300"
            >
                <slot />
            </main>
        </div>
    </div>
</template>
