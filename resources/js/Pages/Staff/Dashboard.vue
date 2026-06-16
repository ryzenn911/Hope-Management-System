<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, usePage, router } from "@inertiajs/vue3";
import { onMounted, computed, ref } from "vue";
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.css";

const props = defineProps({
    todayRecord: Object,
    selectedDate: String,
    isToday: Boolean,
});

const datePickerRef = ref(null);

onMounted(() => {
    flatpickr(datePickerRef.value, {
        defaultDate: props.selectedDate,
        dateFormat: "d-m-Y",
        maxDate: "today",
        disable: [
            function (date) {
                return date.getDay() === 0 || date.getDay() === 6;
            },
        ],
        altInput: true,
        altFormat: "d-m-Y",
        formatDate: (date, format, locale) => {
            const today = new Date();
            if (
                date.getDate() === today.getDate() &&
                date.getMonth() === today.getMonth() &&
                date.getFullYear() === today.getFullYear()
            ) {
                return "Today";
            }
            const dd = String(date.getDate()).padStart(2, "0");
            const mm = String(date.getMonth() + 1).padStart(2, "0");
            const yyyy = date.getFullYear();
            return `${dd}-${mm}-${yyyy}`;
        },
        onChange: (selectedDates, dateStr) => {
            router.get(
                route("staff.dashboard"),
                { date: dateStr },
                {
                    preserveState: true,
                    preserveScroll: true,
                },
            );
        },
    });
});
const isRedirecting = ref(false);

function handleAttendanceClick() {
    if (isRedirecting.value) return;
    isRedirecting.value = true;
    router.visit(route("staff.attendance.scan"), {
        onFinish: () => {
            isRedirecting.value = false;
        },
    });
}

const page = usePage();
const user = computed(() => page.props.auth.user);
</script>

<template>
    <Head title="Dashboard | Hope for Cambodian Children" />
    <AuthenticatedLayout>
        <div class="py-4">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- ផ្នែកព័ត៌មានបុគ្គលិក -->
                <div
                    class="overflow-hidden bg-white shadow-sm rounded-xl px-5 py-3 mx-4 mb-6"
                >
                    <h1 class="md:text-xl text-md text-[#01AAEB] font-poppins">
                        Hello,
                        <span class="uppercase font-bold">{{
                            user?.name_en || "Staff"
                        }}</span
                        >!
                    </h1>
                    <h2 class="md:text-lg text-md text-gray-600 font-siemreap">
                        Position: {{ user?.position?.name || "N/A" }}
                    </h2>
                </div>

                <!-- ផ្នែកប៊ូតុងកាតគ្របគ្រង -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mx-4 mb-6">
                    <button
                        @click="handleAttendanceClick"
                        :disabled="isRedirecting"
                        class="w-full flex flex-col items-center justify-center bg-[#8BC34A] text-white p-6 rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 active:scale-95 group text-center disabled:opacity-70"
                    >
                        <div
                            class="p-4 bg-white/20 rounded-2xl mb-3 group-hover:scale-110 transition-transform duration-300"
                        >
                            <svg
                                v-if="isRedirecting"
                                class="animate-spin w-10 h-10 text-white"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                ></circle>
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                ></path>
                            </svg>
                            <svg
                                v-else
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
                        >
                            {{ isRedirecting ? "LOADING..." : "ATTENDANCE" }}
                        </span>
                        <span
                            class="text-xs text-emerald-100 font-siemreap mt-1"
                            >ស្កេន QR Code វត្តមាន ចូល/ចេញ</span
                        >
                    </button>

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
                    class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-4 rounded-xl shadow-sm border border-gray-100"
                >
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">
                            Attendance History
                        </h1>
                        <p class="text-sm text-gray-500">
                            Select a date to view attendance records
                        </p>
                    </div>

                    <div class="relative w-full sm:w-64">
                        <label
                            class="block text-xs font-semibold text-gray-600 mb-1"
                            >Filter by Date:</label
                        >
                        <input
                            ref="datePickerRef"
                            type="text"
                            class="w-full bg-gray-50 border border-gray-300 text-gray-950 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 font-mono cursor-pointer"
                            placeholder="Select Date"
                            readonly
                        />
                    </div>
                </div>

                <div
                    class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm"
                >
                    <table
                        class="w-full border-collapse text-left text-sm text-gray-500"
                    >
                        <thead
                            class="bg-gray-50 text-xs font-semibold uppercase text-gray-700 font-siemreap whitespace-nowrap"
                        >
                            <tr>
                                <th scope="col" class="px-6 py-4">វេនការងារ</th>
                                <th scope="col" class="px-6 py-4">ម៉ោងចូល</th>
                                <th scope="col" class="px-6 py-4">ម៉ោងចេញ</th>
                                <th scope="col" class="px-6 py-4 text-right">
                                    ស្ថានភាព
                                </th>
                            </tr>
                        </thead>

                        <tbody
                            class="divide-y divide-gray-150 border-t border-gray-150 text-gray-900 font-medium"
                        >
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td
                                    class="px-6 py-4 text-[#01AAEB] font-siemreap font-bold text-xs xl:text-md"
                                >
                                    វេនព្រឹក
                                </td>
                                <td
                                    class="px-6 py-4 font-mono text-xs xl:text-lg"
                                >
                                    {{
                                        props.todayRecord?.check_in_morn ||
                                        "--:--"
                                    }}
                                </td>
                                <td
                                    class="px-6 py-4 font-mono text-xs xl:text-lg"
                                >
                                    {{
                                        props.todayRecord?.check_out_morn ||
                                        "--:--"
                                    }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span
                                        v-if="props.todayRecord?.morn_status"
                                        :class="{
                                            'bg-green-50 text-green-700 ring-green-600/20':
                                                props.todayRecord
                                                    .morn_status === 'Present',
                                            'bg-amber-50 text-amber-700 ring-amber-600/20':
                                                props.todayRecord
                                                    .morn_status === 'Late',
                                            'bg-red-50 text-red-700 ring-red-600/20':
                                                props.todayRecord
                                                    .morn_status === 'Absent',
                                        }"
                                        class="inline-flex items-center rounded-md px-2.5 py-1 xl:text-xs text-[10px] font-bold ring-1 ring-inset"
                                    >
                                        {{
                                            props.todayRecord.morn_status ===
                                            "Present"
                                                ? "PRESENT"
                                                : props.todayRecord
                                                        .morn_status === "Late"
                                                  ? "LATE"
                                                  : "ABSENT"
                                        }}
                                    </span>
                                    <span
                                        v-else
                                        class="text-gray-400 text-xs font-normal"
                                        >មិនទាន់មានទិន្នន័យ</span
                                    >
                                </td>
                            </tr>

                            <tr class="hover:bg-gray-50 transition-colors">
                                <td
                                    class="px-6 py-4 text-[#01AAEB] text-xs xl:text-md font-siemreap font-bold"
                                >
                                    វេនរសៀល
                                </td>
                                <td
                                    class="px-6 py-4 font-mono text-xs xl:text-lg"
                                >
                                    {{
                                        props.todayRecord?.check_in_aft ||
                                        "--:--"
                                    }}
                                </td>
                                <td
                                    class="px-6 py-4 font-mono text-xs xl:text-lg"
                                >
                                    {{
                                        props.todayRecord?.check_out_aft ||
                                        "--:--"
                                    }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span
                                        v-if="props.todayRecord?.aft_status"
                                        :class="{
                                            'bg-green-50 text-green-700 ring-green-600/20':
                                                props.todayRecord.aft_status ===
                                                'Present',
                                            'bg-amber-50 text-amber-700 ring-amber-600/20':
                                                props.todayRecord.aft_status ===
                                                'Late',
                                            'bg-red-50 text-red-700 ring-red-600/20':
                                                props.todayRecord.aft_status ===
                                                'Absent',
                                        }"
                                        class="inline-flex items-center rounded-md px-2.5 py-1 xl:text-xs text-[10px] font-bold ring-1 ring-inset"
                                    >
                                        {{
                                            props.todayRecord.aft_status ===
                                            "Present"
                                                ? "PRESENT"
                                                : props.todayRecord
                                                        .aft_status === "Late"
                                                  ? "LATE"
                                                  : "ABSENT"
                                        }}
                                    </span>
                                    <span
                                        v-else
                                        class="text-gray-400 text-xs font-normal"
                                        >មិនទាន់មានទិន្នន័យ</span
                                    >
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
