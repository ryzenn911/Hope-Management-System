<template>
    <AdminLayout>
        <div class="px-6">
            <div class="flex space-x-6 border-b mb-6">
                <button
                    @click="activeTab = 'logs'"
                    :class="
                        activeTab === 'logs'
                            ? 'text-[#01AAEB] border-b-2 border-[#01AAEB] font-bold'
                            : 'text-gray-500'
                    "
                    class="pb-2 transition-all"
                >
                    Attedance Reports
                </button>
                <button
                    @click="activeTab = 'settings'"
                    :class="
                        activeTab === 'settings'
                            ? 'text-[#01AAEB] border-b-2 border-[#01AAEB] font-bold'
                            : 'text-gray-500'
                    "
                    class="pb-2 transition-all"
                >
                    Settings
                </button>
            </div>

            <div v-if="activeTab === 'logs'">
                <div
                    class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden font-poppins text-[#2d323f]"
                >
                    <div
                        class="p-5 border-b border-gray-50 flex justify-between items-center"
                    >
                        <h3 class="font-bold text-gray-700">
                            Attendance Overview
                        </h3>
                        <div class="flex gap-2">
                            <input
                                type="date"
                                class="text-sm border-gray-200 rounded-lg focus:ring-[#01AAEB]/20 focus:border-[#01AAEB]"
                            />
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="bg-gray-200 border-b border-gray-100 text-sm text-gray-600 font-bold uppercase tracking-widest font-poppins"
                                >
                                    <th class="p-4 border-b">Staff Id</th>
                                    <th class="p-4 border-b">Staff Name</th>
                                    <th
                                        class="p-4 border-b text-center bg-blue-50/30"
                                    >
                                        S1 In
                                    </th>
                                    <th
                                        class="p-4 border-b text-center bg-blue-50/30"
                                    >
                                        S1 Out
                                    </th>
                                    <th
                                        class="p-4 border-b text-center bg-indigo-50/30"
                                    >
                                        S2 In
                                    </th>
                                    <th
                                        class="p-4 border-b text-center bg-indigo-50/30"
                                    >
                                        S2 Out
                                    </th>
                                    <th class="p-4 border-b text-center">
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="text-[11px] divide-y divide-gray-100 text-gray-700">
                                <tr
                                    v-for="log in attendanceLogs"
                                    :key="log.id"
                                    class="hover:bg-gray-50/50 transition-colors border-b border-gray-50"
                                >
                                    <td
                                        class="p-4 font-poppins font-bold text-gray-500"
                                    >
                                        {{ log.employee_code }}
                                    </td>
                                    <td
                                        class="p-4 uppercase font-bold text-gray-500"
                                    >
                                        {{ log.name_en }}
                                    </td>

                                    <td
                                        class="p-4 text-center font-mono font-semibold"
                                        :class="
                                            log.s1_in
                                                ? 'text-blue-600'
                                                : 'text-gray-300'
                                        "
                                    >
                                        {{ log.s1_in ?? "_ _ : _ _" }}
                                    </td>
                                    <td
                                        class="p-4 text-center font-mono font-semibold"
                                        :class="
                                            log.s1_out
                                                ? 'text-blue-600'
                                                : 'text-gray-300'
                                        "
                                    >
                                        {{ log.s1_out ?? "_ _ : _ _" }}
                                    </td>

                                    <td
                                        class="p-4 text-center font-mono font-semibold"
                                        :class="
                                            log.s2_in
                                                ? 'text-indigo-600'
                                                : 'text-gray-300'
                                        "
                                    >
                                        {{ log.s2_in ?? "_ _ : _ _" }}
                                    </td>
                                    <td
                                        class="p-4 text-center font-mono font-semibold"
                                        :class="
                                            log.s2_out
                                                ? 'text-indigo-600'
                                                : 'text-gray-300'
                                        "
                                    >
                                        {{ log.s2_out ?? "_ _ : _ _" }}
                                    </td>

                                    <td class="p-4 text-center">
                                        <span
                                            v-if="log.status === 'present'"
                                            class="px-3 py-1 rounded-md text-[10px] font-black uppercase bg-green-100 text-green-600"
                                        >
                                            Present
                                        </span>
                                        <span
                                            v-else-if="log.status === 'late'"
                                            class="px-3 py-1 rounded-md text-[10px] font-black uppercase bg-orange-100 text-orange-600"
                                        >
                                            Late
                                        </span>
                                        <span
                                            v-else-if="log.status === 'absent'"
                                            class="px-3 py-1 rounded-md text-[10px] font-black uppercase bg-red-100 text-red-600"
                                        >
                                            Absent
                                        </span>
                                        <span
                                            v-else
                                            class="px-3 py-1 rounded-md text-[10px] font-black uppercase bg-gray-100 text-gray-400"
                                        >
                                            Pending
                                        </span>
                                    </td>
                                </tr>

                                <tr v-if="attendanceLogs?.length === 0">
                                    <td
                                        colspan="7"
                                        class="p-10 text-center text-gray-400 italic"
                                    >
                                        No attendance records found for this
                                        date.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div v-if="activeTab === 'settings'">
                <Settings :location="location" :employees="employees" />
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import Settings from "./Settings.vue";
import { ref } from "vue";

const props = defineProps({
    location: Object,
    employees: Array,
    attendanceSettings: Array,
    attendanceLogs: Array,
});

const activeTab = ref("logs");
</script>
