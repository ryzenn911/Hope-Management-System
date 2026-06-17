<script setup>
import { ref, onMounted } from "vue";
import { router } from "@inertiajs/vue3";
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.css";
import AdminLayout from "@/Layouts/AdminLayout.vue";

const props = defineProps({
    employees: Array,
    selectedDate: String,
});

const currentDate = ref(props.selectedDate);
const adminDatePickerRef = ref(null);

onMounted(() => {
    flatpickr(adminDatePickerRef.value, {
        defaultDate: currentDate.value,
        dateFormat: "d-m-Y",
        // សម្រាប់ Admin យើងមិនបិទថ្ងៃសៅរ៍ អាទិត្យ ទេ ដើម្បីងាយស្រួលពិនិត្យទិន្នន័យចាស់ៗ
        onChange: (selectedDates, dateStr) => {
            currentDate.value = dateStr;
            router.get(
                route("admin.attendance.index"),
                { date: dateStr },
                {
                    preserveState: true,
                    preserveScroll: true,
                },
            );
        },
    });
});
</script>

<template>
    <AdminLayout>
        <div class="p-6 max-w-7xl mx-auto font-siemreap">
            <div
                class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-4 rounded-xl shadow-sm border border-gray-200"
            >
                <div>
                    <h1 class="text-xl font-bold text-gray-800">
                        គ្រប់គ្រងវត្តមានបុគ្គលិក
                    </h1>
                    <p class="text-sm text-gray-500">
                        ពិនិត្យ និងកែសម្រួលទិន្នន័យវត្តមានប្រចាំថ្ងៃ
                    </p>
                </div>

                <div class="relative w-full sm:w-64">
                    <label
                        class="block text-xs font-semibold text-gray-600 mb-1"
                        >ជ្រើសរើសថ្ងៃខែ៖</label
                    >
                    <input
                        ref="adminDatePickerRef"
                        type="text"
                        class="w-full bg-gray-50 border border-gray-300 text-gray-950 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 font-mono cursor-pointer"
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
                        class="bg-gray-50 text-xs font-semibold uppercase text-gray-700 whitespace-nowrap"
                    >
                        <tr>
                            <th scope="col" class="px-6 py-4">ឈ្មោះបុគ្គលិក</th>
                            <th scope="col" class="px-6 py-4">មុខតំណែង</th>
                            <th scope="col" class="px-6 py-4">
                                វេនព្រឹក (ចូល/ចេញ)
                            </th>
                            <th scope="col" class="px-6 py-4">ស្ថានភាពព្រឹក</th>
                            <th scope="col" class="px-6 py-4">
                                វេនរសៀល (ចូល/ចេញ)
                            </th>
                            <th scope="col" class="px-6 py-4">ស្ថានភាពរសៀល</th>
                        </tr>
                    </thead>
                    <tbody
                        class="divide-y divide-gray-150 border-t border-gray-150 text-gray-900 font-medium"
                    >
                        <tr
                            v-for="emp in props.employees"
                            :key="emp.id"
                            class="hover:bg-gray-50 transition-colors"
                        >
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900">
                                    {{ emp.name_kh }}
                                </div>
                                <div class="text-xs text-gray-400 font-mono">
                                    {{ emp.name_eng }}
                                </div>
                            </td>
                            <td
                                class="px-6 py-4 text-xs font-siemreap text-gray-600"
                            >
                                {{ emp.position }}
                            </td>
                            <td
                                class="px-6 py-4 font-mono text-xs text-blue-600"
                            >
                                {{ emp.check_in_morn }} /
                                {{ emp.check_out_morn }}
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    v-if="emp.morn_status"
                                    :class="{
                                        'bg-green-50 text-green-700 ring-green-600/20':
                                            emp.morn_status === 'Present',
                                        'bg-amber-50 text-amber-700 ring-amber-600/20':
                                            emp.morn_status === 'Late',
                                        'bg-red-50 text-red-700 ring-red-600/20':
                                            emp.morn_status === 'Absent',
                                    }"
                                    class="inline-flex items-center rounded-md px-2 py-0.5 text-[10px] font-bold ring-1 ring-inset uppercase"
                                >
                                    {{ emp.morn_status }}
                                </span>
                                <span
                                    v-else
                                    class="text-gray-400 text-xs font-normal"
                                    >មិនទាន់មានទិន្នន័យ</span
                                >
                            </td>
                            <td
                                class="px-6 py-4 font-mono text-xs text-indigo-600"
                            >
                                {{ emp.check_in_aft }} / {{ emp.check_out_aft }}
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    v-if="emp.aft_status"
                                    :class="{
                                        'bg-green-50 text-green-700 ring-green-600/20':
                                            emp.aft_status === 'Present',
                                        'bg-amber-50 text-amber-700 ring-amber-600/20':
                                            emp.aft_status === 'Late',
                                        'bg-red-50 text-red-700 ring-red-600/20':
                                            emp.aft_status === 'Absent',
                                    }"
                                    class="inline-flex items-center rounded-md px-2 py-0.5 text-[10px] font-bold ring-1 ring-inset uppercase"
                                >
                                    {{ emp.aft_status }}
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
    </AdminLayout>
</template>
