<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, usePage, Link, router } from "@inertiajs/vue3";
import { computed, ref, onMounted, defineProps } from "vue";
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.css";

const props = defineProps({
    leaves: Array,
    leaveBalance: Number,
    approvedDays: Number,
    rejectedDays: Number,
    pendingDays: Number,
    filters: Object,
});
const page = usePage();
const user = computed(() => page.props.auth.user);
const isDropdownOpen = ref(false);

const notifications = computed(() => page.props.auth.notifications);
const isNotiOpen = ref(false);

// Function សម្រាប់ Mark as read ទាំងអស់
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

const datePicker = ref(null);
// បង្កើត String សម្រាប់បង្ហាញក្នុង Input (ឧទាហរណ៍៖ 2026-05)
const selectedDate = ref(`${props.filters.year}-${props.filters.month}`);

onMounted(() => {
    flatpickr(datePicker.value, {
        defaultDate: selectedDate.value,
        dateFormat: "Y-m",
        altInput: true,
        altFormat: "F Y",
        theme: "light",
        onChange: (selectedDates, dateStr) => {
            const [year, month] = dateStr.split("-");
            router.get(
                route("staff.dashboard"),
                { month, year },
                { preserveState: true, replace: true },
            );
        },
    });
});
</script>

<template>
    <Head title="Dashboard | Hope for Cambodian Children" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
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
        </template>

        <div class="py-4">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm rounded-xl px-5 py-3 mx-4 mb-6"
                >
                    <h1 class="md:text-xl text-sm text-[#01AAEB] font-poppins">
                        Hello,
                        <span class="uppercase font-bold">{{
                            user.name_en
                        }}</span
                        >!
                    </h1>
                    <h2 class="md:text-lg text-sm text-gray-600 font-siemreap">
                        Position:
                        {{ $page.props.auth.user.position?.name || "N/A" }}
                    </h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mx-4 mb-6">
                    <Link
                        :href="route('staff.attendance.scan')"
                        class="flex flex-col items-center justify-center bg-[#8BC34A] text-white p-6 rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 active:scale-95 group text-center"
                    >
                        <div
                            class="p-4 bg-white/20 rounded-2xl mb-3 group-hover:scale-110 transition-transform duration-300"
                        >
                            <svg
                                class="w-10 h-10 text-white"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                ></path>
                            </svg>
                        </div>
                        <span
                            class="text-lg font-bold font-poppins tracking-wide"
                            >ATTENDANCE</span
                        >
                        <span
                            class="text-xs text-emerald-100 font-siemreap mt-1"
                            >ស្កេនកូដវត្តមាន ចូល/ចេញ</span
                        >
                    </Link>

                    <Link
                        :href="route('staff.leaves.index')"
                        class="flex flex-col items-center justify-center bg-[#01AAEB] text-white p-6 rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 active:scale-95 group text-center"
                    >
                        <div
                            class="p-4 bg-white/20 rounded-2xl mb-3 group-hover:scale-110 transition-transform duration-300"
                        >
                            <svg
                                class="w-10 h-10 text-white"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                ></path>
                            </svg>
                        </div>
                        <span
                            class="text-lg font-bold font-poppins tracking-wide"
                            >LEAVE REQUEST</span
                        >
                        <span class="text-xs text-sky-100 font-siemreap mt-1"
                            >ស្នើសុំច្បាប់ឈប់សម្រាក</span
                        >
                    </Link>
                </div>
                <div
                    class="flex flex-col md:flex-row md:items-center md:justify-between px-5 mb-6 gap-4"
                >
                    <div class="flex items-center space-x-2">
                        <div class="p-2 bg-blue-50 rounded-lg">
                            <svg
                                class="w-5 h-5 text-[#01AAEB]"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                />
                            </svg>
                        </div>
                        <div>
                            <h3
                                class="text-gray-800 font-bold font-siemreap text-base leading-tight"
                            >
                                វត្តមានប្រចាំសប្ដាហ៍
                            </h3>
                            <p class="text-[11px] text-gray-500 font-poppins">
                                Weekly Attendance (Mon - Fri)
                            </p>
                        </div>
                    </div>

                    <div
                        class="flex items-center space-x-2 bg-white border border-gray-200 rounded-2xl p-1.5 shadow-sm"
                    >
                        <button
                            @click="changeWeek('prev')"
                            class="p-2 hover:bg-gray-100 rounded-xl text-gray-500 transition-all active:scale-90"
                        >
                            <svg
                                class="w-5 h-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15 19l-7-7 7-7"
                                />
                            </svg>
                        </button>
                        <div class="px-4 text-center">
                            <span
                                class="block text-[10px] text-gray-400 uppercase font-bold tracking-widest font-poppins"
                                >Current Week</span
                            >
                            <span
                                class="text-sm font-bold text-gray-700 font-poppins"
                                >June 15 - June 19, 2026</span
                            >
                        </div>
                        <button
                            @click="changeWeek('next')"
                            class="p-2 hover:bg-gray-100 rounded-xl text-gray-500 transition-all active:scale-90"
                        >
                            <svg
                                class="w-5 h-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 5l7 7-7 7"
                                />
                            </svg>
                        </button>
                    </div>
                </div>

                <div
                    class="overflow-hidden bg-white rounded-3xl border border-gray-100 shadow-sm mx-5 mb-10"
                >
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="bg-gray-50/80 border-b border-gray-100"
                                >
                                    <th
                                        class="p-4 text-[11px] font-bold text-gray-400 uppercase font-poppins w-40"
                                    >
                                        Date / Day
                                    </th>
                                    <th
                                        colspan="2"
                                        class="p-4 text-[11px] font-bold text-blue-500 uppercase font-poppins text-center border-x border-gray-100/50"
                                    >
                                        Morning Session
                                    </th>
                                    <th
                                        colspan="2"
                                        class="p-4 text-[11px] font-bold text-indigo-500 uppercase font-poppins text-center border-r border-gray-100/50"
                                    >
                                        Afternoon Session
                                    </th>
                                    <th
                                        class="p-4 text-[11px] font-bold text-gray-400 uppercase font-poppins text-center"
                                    >
                                        Status
                                    </th>
                                </tr>
                                <tr
                                    class="bg-gray-50/30 border-b border-gray-100 text-[10px] uppercase text-gray-400 font-bold"
                                >
                                    <th></th>
                                    <th
                                        class="text-center py-2 border-r border-gray-100/50"
                                    >
                                        In 1
                                    </th>
                                    <th
                                        class="text-center py-2 border-r border-gray-100/50"
                                    >
                                        Out 1
                                    </th>
                                    <th
                                        class="text-center py-2 border-r border-gray-100/50"
                                    >
                                        In 2
                                    </th>
                                    <th
                                        class="text-center py-2 border-r border-gray-100/50"
                                    >
                                        Out 2
                                    </th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr
                                    v-for="day in 5"
                                    :key="day"
                                    class="hover:bg-blue-50/30 transition-colors group"
                                >
                                    <td class="p-4">
                                        <div
                                            class="text-sm font-bold text-gray-800 font-poppins"
                                        >
                                            June 15, 2026
                                        </div>
                                        <div
                                            class="text-[11px] text-gray-400 font-siemreap"
                                        >
                                            ថ្ងៃច័ន្ទ / Monday
                                        </div>
                                    </td>

                                    <td
                                        class="p-4 text-center border-x border-gray-100/30"
                                    >
                                        <span
                                            class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg"
                                            >07:45 AM</span
                                        >
                                    </td>
                                    <td
                                        class="p-4 text-center border-r border-gray-100/30"
                                    >
                                        <span
                                            class="text-xs font-bold text-gray-500 bg-gray-100 px-2 py-1 rounded-lg"
                                            >12:05 PM</span
                                        >
                                    </td>

                                    <td
                                        class="p-4 text-center border-r border-gray-100/30"
                                    >
                                        <span
                                            class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded-lg"
                                            >01:30 PM</span
                                        >
                                    </td>
                                    <td
                                        class="p-4 text-center border-r border-gray-100/30"
                                    >
                                        <span
                                            class="text-xs font-bold text-gray-500 bg-gray-100 px-2 py-1 rounded-lg"
                                            >17:00 PM</span
                                        >
                                    </td>

                                    <td class="p-4 text-center">
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-emerald-50 text-emerald-600 border border-emerald-100"
                                        >
                                            Present
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
