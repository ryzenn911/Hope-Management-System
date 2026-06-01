<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.css";
import Swal from "sweetalert2";

const props = defineProps({
    leaves: Array,
    filters: Object,
});

const datePicker = ref(null);

onMounted(() => {
    flatpickr(datePicker.value, {
        dateFormat: "Y-m",
        altInput: true,
        altFormat: "F Y",
        defaultDate: `${props.filters.year}-${props.filters.month}`,
        onChange: (selectedDates, dateStr) => {
            const [year, month] = dateStr.split("-");
            router.get(
                route("admin.leaves.index"),
                { month, year },
                {
                    preserveState: true,
                    replace: true,
                },
            );
        },
    });
});

const updateStatus = (id, status) => {
    const isApprove = status === "approved";

    Swal.fire({
        title: isApprove ? "Approve Request?" : "Reject Request?",
        text: `Are you sure you want to ${isApprove ? "approve" : "reject"} this leave request?`,
        icon: isApprove ? "question" : "warning",
        showCancelButton: true,
        confirmButtonColor: isApprove ? "#8BC34A" : "#d33",
        cancelButtonColor: "#6e7881",
        confirmButtonText: isApprove ? "Yes, Approve it!" : "Yes, Reject it!",
        cancelButtonText: "Cancel",
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            router.patch(
                route("admin.leaves.updateStatus", id),
                { status },
                {
                    onSuccess: () => {
                        Swal.fire({
                            title: "Updated!",
                            text: `The request has been ${isApprove ? "approved" : "rejected"} successfully.`,
                            icon: "success",
                            timer: 1500,
                            showConfirmButton: false,
                            toast: true,
                            position: "top-end",
                        });
                    },
                },
            );
        }
    });
};
</script>

<template>
    <Head title="Leave Requests | Hope for Cambodia Children" />
    <AdminLayout>
        <div class="md:p-6 p-4 bg-gray-50 md:mx-5 mx-0">
            <div
                class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4"
            >
                <div>
                    <h2 class="md:text-2xl text-md font-bold text-gray-800">
                        Leave Requests
                    </h2>
                    <p class="text-sm text-gray-500">
                        View and manage staff leave requests.
                    </p>
                </div>

                <div class="relative max-w-[200px]">
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
                        class="block w-full pl-10 pr-3 py-4 border border-gray-200 rounded-xl text-sm font-medium text-gray-600 focus:ring-[#01AAEB] focus:border-[#01AAEB] cursor-pointer bg-white shadow-sm"
                    />
                </div>
            </div>

            <div class="mt-1">
                <div
                    class="max-w-8xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 get-scrollbar overflow-x-auto md:overflow-x-visibl"
                >
                    <table
                        class="w-full text-left border-collapse whitespace-nowrap"
                    >
                        <thead class="bg-gray-300">
                            <tr>
                                <th
                                    class="md:p-4 p-2 text-xs font-bold text-gray-700 uppercase tracking-wider"
                                >
                                    Staff ID
                                </th>
                                <th
                                    class="md:p-4 p-2 text-xs font-bold text-gray-700 uppercase tracking-wider"
                                >
                                    Staff Name
                                </th>
                                <th
                                    class="md:p-4 p-2 text-xs font-bold text-gray-700 uppercase tracking-wider"
                                >
                                    Leave Type
                                </th>
                                <th
                                    class="md:p-4 p-2 text-xs font-bold text-gray-700 uppercase tracking-wider"
                                >
                                    Start Date
                                </th>
                                <th
                                    class="md:p-4 p-2 text-xs font-bold text-gray-700 uppercase tracking-wider"
                                >
                                    End Date
                                </th>
                                <th
                                    class="md:p-4 p-2 text-xs font-bold text-gray-700 uppercase tracking-wider"
                                >
                                    Duration
                                </th>
                                <th
                                    class="md:p-4 p-2 text-xs font-bold text-gray-700 uppercase tracking-wider"
                                >
                                    Reason
                                </th>
                                <th
                                    class="md:p-4 p-2 text-xs font-bold text-gray-700 uppercase tracking-wider"
                                >
                                    Status
                                </th>
                                <th
                                    class="md:p-4 p-2 text-xs font-bold text-gray-700 uppercase tracking-wider text-center"
                                >
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr
                                v-for="leave in leaves"
                                :key="leave.id"
                                class="hover:bg-gray-50/30 transition-colors"
                            >
                                <td
                                    class="md:p-4 p-2 md:text-sm text-xs font-medium text-gray-600"
                                >
                                    #{{ leave.employee?.employee_code }}
                                </td>
                                <td class="md:p-4 p-2">
                                    <div class="flex items-center gap-3">
                                        <div>
                                            <div
                                                class="md:text-sm text-xs font-bold text-gray-700 uppercase"
                                            >
                                                {{ leave.employee?.user?.name }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="md:p-4 p-2">
                                    <span
                                        class="md:text-sm text-xs text-gray-600"
                                        >{{ leave.leave_type }}</span
                                    >
                                </td>
                                <td class="md:p-4 p-2">
                                    <span
                                        class="md:text-sm text-xs text-gray-600"
                                        >{{ leave.start_date }}</span
                                    >
                                </td>
                                <td class="md:p-4 p-2">
                                    <span
                                        class="md:text-sm text-xs text-gray-600"
                                        >{{ leave.end_date }}</span
                                    >
                                </td>
                                <td class="md:p-4 p-2">
                                    <div
                                        class="md:text-sm text-xs text-gray-700 font-medium"
                                    >
                                        {{ leave.total_days }} Days
                                    </div>
                                </td>
                                <td class="md:p-4 p-2">
                                    <span
                                        class="md:text-sm text-xs text-gray-600 font-siemreap"
                                        >{{ leave.reason }}</span
                                    >
                                </td>
                                <td class="md:p-4 p-2">
                                    <span
                                        :class="{
                                            'bg-amber-50 text-amber-600 border-amber-100':
                                                leave.status === 'pending',
                                            'bg-emerald-50 text-emerald-600 border-emerald-100':
                                                leave.status === 'approved',
                                            'bg-rose-50 text-rose-600 border-rose-100':
                                                leave.status === 'rejected',
                                        }"
                                        class="px-3 py-1 rounded-full text-[11px] font-bold uppercase border"
                                    >
                                        {{ leave.status }}
                                    </span>
                                </td>
                                <td class="md:p-4 p-2 text-center">
                                    <div
                                        v-if="leave.status === 'pending'"
                                        class="flex justify-center gap-2"
                                    >
                                        <button
                                            @click="
                                                updateStatus(
                                                    leave.id,
                                                    'approved',
                                                )
                                            "
                                            class="p-2 bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-100 transition-colors shadow-sm border border-emerald-100"
                                            title="Approve"
                                        >
                                            Approve
                                        </button>
                                        <button
                                            @click="
                                                updateStatus(
                                                    leave.id,
                                                    'rejected',
                                                )
                                            "
                                            class="p-2 bg-rose-50 text-rose-600 rounded-lg hover:bg-rose-100 transition-colors shadow-sm border border-rose-100"
                                            title="Reject"
                                        >
                                            Reject
                                        </button>
                                    </div>
                                    <div
                                        v-else
                                        class="text-xs text-gray-400 italic pr-2"
                                    >
                                        Reviewed
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="leaves.length === 0">
                                <td colspan="9" class="p-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="text-gray-300 mb-2">
                                            <svg
                                                class="w-12 h-12"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="1"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                                ></path>
                                            </svg>
                                        </div>
                                        <p class="text-gray-400">
                                            No leave requests found for the
                                            selected month.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
