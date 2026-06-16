<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { onMounted, defineProps, ref } from "vue";
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

// const datePicker = ref(null);
// const selectedDate = ref(`${props.filters.year}-${props.filters.month}`);

// onMounted(() => {
//     flatpickr(datePicker.value, {
//         defaultDate: selectedDate.value,
//         dateFormat: "Y-m",
//         altInput: true,
//         altFormat: "F Y",
//         theme: "light",
//         onChange: (selectedDates, dateStr) => {
//             const [year, month] = dateStr.split("-");
//             router.get(
//                 route("staff.dashboard"),
//                 { month, year },
//                 { preserveState: true, replace: true },
//             );
//         },
//     });
// });
</script>

<template>
    <Head title="Leave | Hope for Cambodian Children" />
    <AuthenticatedLayout>
        <div class="mx-auto max-w-6xl">
            <div class="flex items-center justify-between px-5 mb-2 mt-5">
                <h3
                    class="text-gray-700 font-bold font-siemreap text-sm md:text-base"
                >
                    របាយការណ៍ប្រចាំខែ
                </h3>
                <!-- <div class="relative max-w-[160px]">
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
                    class="block w-full pl-10 pr-3 py-2 border border-gray-200 rounded-lg text-xs font-semibold text-gray-600 focus:ring-[#01AAEB] focus:border-[#01AAEB] cursor-pointer bg-white shadow-sm"
                    placeholder="Select Month"
                />
            </div> -->
                <Link
                    :href="route('staff.leaves.create')"
                    class="flex flex-col items-center justify-center bg-[#01AAEB] text-white p-2 rounded-lg shadow-md hover:shadow-xl transition-all duration-300 active:scale-95 text-center cursor-pointer"
                >
                    <span>Request Leave</span></Link
                >
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
                        <tr class="bg-gray-100 border-b border-gray-200">
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
                                    class="text-gray-500 font-bold md:text-sm text-[10px]"
                                    >{{ leave.total_days }} Day</span
                                >
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
                                    class="px-2 py-1 rounded-full md:text-[10px] text-[9px] font-bold uppercase tracking-tight border"
                                >
                                    {{ leave.status }}
                                </span>
                            </td>
                        </tr>
                        <!-- <tr v-if="leaves.length === 0">
                        <td
                            colspan="5"
                            class="p-8 text-center text-gray-400 text-sm"
                        >
                            No leave requests found.
                        </td>
                    </tr> -->
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
