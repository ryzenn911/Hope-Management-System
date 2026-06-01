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

                        <!-- Dropdown បញ្ជីសារ -->
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
                                    <p class="text-sm font-bold text-gray-800">
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
                            {{ $page.props.auth.user.first_letter }}
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

                            <!-- Divider -->
                            <div class="border-t border-gray-50 my-1"></div>

                            <!-- Logout Item -->
                            <Link
                                href="/logout"
                                method="post"
                                as="button"
                                class="w-full flex items-center p-1 text-sm text-red-600 hover:bg-red-50"
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
                    class="overflow-hidden bg-white shadow-sm rounded-xl px-5 py-3 mx-4"
                >
                    <h1 class="md:text-xl text-sm text-[#01AAEB] font-poppins">
                        Hello,
                        <span class="uppercase font-bold">{{
                            user.name_en
                        }}</span
                        >!
                    </h1>
                    <h2 class="md:text-lg text-sm text-gray-600 font-poppins">
                        Position:
                        {{ $page.props.auth.user.position?.name || "N/A" }}
                    </h2>
                </div>
                <div class="flex items-center justify-end px-5">
                    <div class="p-4">
                        <div class="flex justify-end">
                            <div class="relative max-w-[160px]">
                                <div
                                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                                >
                                    <svg
                                        class="h-4 w-4 text-gray-400"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                        />
                                    </svg>
                                </div>
                                <input
                                    ref="datePicker"
                                    type="text"
                                    class="block w-full pl-10 pr-3 md:py-4 py-2 border border-gray-200 rounded-lg text-xs font-semibold text-gray-600 focus:ring-[#01AAEB] focus:border-[#01AAEB] cursor-pointer bg-white shadow-sm"
                                    placeholder="Select Month"
                                />
                            </div>
                        </div>

                        <div
                            class="grid grid-cols-2 md:grid-cols-4 md:gap-4 gap-2"
                        ></div>
                    </div>
                    <div class="flex items-center justify-end">
                        <Link
                            href="/staff/leaves/create"
                            class="md:w-40 w-32 flex items-center justify-center bg-[#01AAEB] hover:bg-[#13b3f3] text-white font-bold py-2 md:px-4 px-1 md:py-3 rounded-xl shadow-sm transition-all active:scale-95"
                        >
                            <svg
                                class="w-5 h-5"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    fill="currentColor"
                                    d="M11 13H5v-2h6V5h2v6h6v2h-6v6h-2z"
                                />
                            </svg>
                            <span class="text-xs md:text-base"
                                >Request Leave</span
                            >
                        </Link>
                    </div>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 md:gap-4 gap-2 p-4">
                    <div class="p-4 rounded-2xl shadow-sm bg-[#8BC34A]">
                        <div
                            class="text-white text-xs md:text-md mb-1 font-poppins"
                        >
                            Annual Leave Balance
                        </div>
                        <div
                            class="md:text-xl text-sm font-bold text-white font-poppins"
                        >
                            {{ leaveBalance }}
                            <span class="font-normal text-xs">Days</span>
                        </div>
                    </div>

                    <div class="bg-[#01AAEB] p-4 rounded-2xl shadow-sm">
                        <div
                            class="text-white text-xs md:text-md mb-1 font-poppins"
                        >
                            Approved Requests
                        </div>
                        <div
                            class="md:text-xl text-sm font-bold text-white font-poppins"
                        >
                            {{ approvedDays }}
                            <span class="font-normal text-xs">Days</span>
                        </div>
                    </div>

                    <div class="bg-[#F44336] p-4 rounded-2xl shadow-sm">
                        <div
                            class="text-white text-xs md:text-md mb-1 font-poppins"
                        >
                            Rejected Requests
                        </div>
                        <div
                            class="md:text-xl text-sm font-bold text-white font-poppins"
                        >
                            {{ rejectedDays }}
                            <span class="font-normal text-xs">Days</span>
                        </div>
                    </div>

                    <div class="bg-[#FFC107] p-4 rounded-2xl shadow-sm">
                        <div
                            class="text-white text-xs md:text-md mb-1 font-poppins"
                        >
                            Pending Requests
                        </div>
                        <div
                            class="md:text-xl text-sm font-bold text-white font-poppins"
                        >
                            {{ pendingDays }}
                            <span class="font-normal text-xs">Days</span>
                        </div>
                    </div>
                </div>
                <div
                    class="mt-6 overflow-hidden bg-white rounded-2xl border border-gray-100 shadow-sm mx-5"
                >
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-300 border-b border-gray-100">
                                <th
                                    class="p-4 text-left md:text-xs text-[9px] font-bold text-gray-700 uppercase tracking-wider"
                                >
                                    Leave Type
                                </th>
                                <th
                                    class="p-4 text-center md:text-xs text-[9px] font-bold text-gray-700 uppercase tracking-wider"
                                >
                                    Start Date
                                </th>
                                <th
                                    class="p-4 text-center md:text-xs text-[9px] font-bold text-gray-700 uppercase tracking-wider"
                                >
                                    End Date
                                </th>
                                <th
                                    class="p-4 text-center md:text-xs text-[9px] font-bold text-gray-700 uppercase tracking-wider"
                                >
                                    Duration
                                </th>
                                <th
                                    class="p-4 text-center md:text-xs text-[9px] font-bold text-gray-700 uppercase tracking-wider"
                                >
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr
                                v-for="leave in leaves"
                                :key="leave.id"
                                class="hover:bg-gray-50/80 transition-colors duration-200"
                            >
                                <td class="p-4">
                                    <span
                                        class="md:text-sm text-[10px] text-gray-500"
                                        >{{ leave.leave_type }}</span
                                    >
                                </td>
                                <td
                                    class="p-4 text-center md:text-sm text-[10px] text-gray-500"
                                >
                                    {{ leave.start_date }}
                                </td>
                                <td
                                    class="p-4 text-center md:text-sm text-[10px] text-gray-500"
                                >
                                    {{ leave.end_date }}
                                </td>
                                <td class="p-4 text-center">
                                    <span
                                        class="p-4 text-gray-500 font-bold md:text-sm text-[10px]"
                                    >
                                        {{ leave.total_days }} Day
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    <span
                                        :class="{
                                            'text-amber-600 bg-amber-50 border-amber-100':
                                                leave.status === 'pending',
                                            'text-emerald-600 bg-emerald-50 border-emerald-100':
                                                leave.status === 'approved',
                                            'text-rose-600 bg-rose-50 border-rose-100':
                                                leave.status === 'rejected',
                                        }"
                                        class="px-1 py-1 rounded-full md:text-[10px] text-[9px] font-bold uppercase tracking-tight border"
                                    >
                                        {{ leave.status }}
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="leaves.length === 0">
                                <td
                                    colspan="6"
                                    class="p-8 text-center text-gray-400 text-sm"
                                >
                                    No leave requests found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
