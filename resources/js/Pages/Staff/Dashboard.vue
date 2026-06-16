<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, usePage, router } from "@inertiajs/vue3";
import { Html5Qrcode } from "html5-qrcode";
import { computed, ref } from "vue";

const props = defineProps({
    weeklyRecords: Array,
    weekRange: String,
    currentDate: String,
});

const isCheckingPermission = ref(false);

async function handleAttendanceClick() {
    if (isCheckingPermission.value) return;
    isCheckingPermission.value = true;

    try {
        // ដាស់ផ្ទាំង Alert សុំសិទ្ធិកាមេរ៉ាពី Browser ភ្លាមៗ
        await Html5Qrcode.getCameras();

        // បើ User ចុច "Allow" វានឹងរត់មកជួរនេះ រួចបញ្ជូនទៅទំព័រ Scan តែម្ដង
        router.visit(route("staff.attendance.scan"));
    } catch (error) {
        // បើ User បដិសេធ (Block) ឬគ្មានកាមេរ៉ា
        alert(
            "សូមអនុញ្ញាតឱ្យកម្មវិធីប្រើប្រាស់កាមេរ៉ា (Allow Camera Permission) ដើម្បីអាចស្កេនវត្តមានបាន។",
        );
    } finally {
        isCheckingPermission.value = false;
    }
}

const page = usePage();
const user = computed(() => page.props.auth.user);

const changeWeek = (direction) => {
    let date = new Date(props.currentDate);
    if (direction === "next") {
        date.setDate(date.getDate() + 7);
    } else {
        date.setDate(date.getDate() - 7);
    }

    // បាញ់ទៅ Controller វិញតាមរយៈ Query String
    router.get(
        route("staff.dashboard"),
        { date: date.toISOString().split("T")[0] },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
};
</script>

<template>
    <Head title="Dashboard | Hope for Cambodian Children" />
    <AuthenticatedLayout>
        <div class="py-4">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm rounded-xl px-5 py-3 mx-4 mb-6"
                >
                    <h1 class="md:text-xl text-md text-[#01AAEB] font-poppins">
                        Hello,
                        <span class="uppercase font-bold">{{
                            user.name_en
                        }}</span
                        >!
                    </h1>
                    <h2 class="md:text-lg text-md text-gray-600 font-siemreap">
                        Position:
                        {{ $page.props.auth.user.position?.name || "N/A" }}
                    </h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mx-4 mb-6">
                    <button
                        @click="handleAttendanceClick"
                        :disabled="isCheckingPermission"
                        class="w-full flex flex-col items-center justify-center bg-[#8BC34A] text-white p-6 rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 active:scale-95 group text-center disabled:opacity-70"
                    >
                        <div
                            class="p-4 bg-white/20 rounded-2xl mb-3 group-hover:scale-110 transition-transform duration-300"
                        >
                            <svg
                                v-if="isCheckingPermission"
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
                            {{
                                isCheckingPermission
                                    ? "CHECKING..."
                                    : "ATTENDANCE"
                            }}
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
                                >Selected Week</span
                            >
                            <span
                                class="text-sm font-bold text-gray-700 font-poppins"
                                >{{ props.weekRange }}</span
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
                                        class="p-4 text-[11px] font-bold text-gray-400 uppercase font-poppins w-44"
                                    >
                                        Day / Date
                                    </th>
                                    <th
                                        class="p-2 text-[11px] font-bold text-blue-500 uppercase font-poppins text-center border-l border-gray-100/50"
                                    >
                                        Morn In
                                    </th>
                                    <th
                                        class="p-2 text-[11px] font-bold text-blue-500 uppercase font-poppins text-center border-r border-gray-100/50"
                                    >
                                        Morn Out
                                    </th>
                                    <th
                                        class="p-2 text-[11px] font-bold text-indigo-500 uppercase font-poppins text-center"
                                    >
                                        Aft In
                                    </th>
                                    <th
                                        class="p-2 text-[11px] font-bold text-indigo-500 uppercase font-poppins text-center border-r border-gray-100/50"
                                    >
                                        Aft Out
                                    </th>
                                    <th
                                        class="p-4 text-[11px] font-bold text-gray-400 uppercase font-poppins text-center w-32"
                                    >
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr
                                    v-for="item in props.weeklyRecords"
                                    :key="item.date"
                                    class="hover:bg-blue-50/30 transition-colors group whitespace-nowrap"
                                >
                                    <td class="p-4">
                                        <div
                                            class="text-sm font-bold text-gray-800 font-poppins"
                                        >
                                            {{ item.display_date }}
                                        </div>
                                        <div
                                            class="text-[11px] text-gray-400 font-siemreap"
                                        >
                                            ថ្ងៃ{{ item.kh_day }} /
                                            {{ item.day_name }}
                                        </div>
                                    </td>

                                    <td
                                        class="p-4 text-center border-l border-gray-100/30"
                                    >
                                        <span
                                            v-if="item.record?.check_in_morn"
                                            :class="{
                                                'text-emerald-600 bg-emerald-50':
                                                    item.record.morn_status?.toLowerCase() ===
                                                    'present',
                                                'text-amber-600 bg-amber-50':
                                                    item.record.morn_status?.toLowerCase() ===
                                                    'late',
                                            }"
                                            class="text-xs font-bold px-2.5 py-1 rounded-lg block mx-auto w-max border border-transparent"
                                        >
                                            {{ item.record.check_in_morn }}
                                            <span
                                                class="text-[9px] block font-normal font-siemreap"
                                            >
                                                {{
                                                    item.record.morn_status?.toLowerCase() ===
                                                    "late"
                                                        ? "យឺត"
                                                        : "ទាន់"
                                                }}
                                            </span>
                                        </span>
                                        <span
                                            v-else
                                            class="text-gray-300 text-xs font-poppins"
                                            >-- : --</span
                                        >
                                    </td>

                                    <td
                                        class="p-4 text-center border-r border-gray-100/30"
                                    >
                                        <span
                                            v-if="item.record?.check_out_morn"
                                            class="text-xs font-bold text-gray-500 bg-gray-100 px-2.5 py-1 rounded-lg"
                                        >
                                            {{ item.record.check_out_morn }}
                                        </span>
                                        <span
                                            v-else
                                            class="text-gray-300 text-xs font-poppins"
                                            >-- : --</span
                                        >
                                    </td>

                                    <td class="p-4 text-center">
                                        <span
                                            v-if="item.record?.check_in_aft"
                                            :class="{
                                                'text-indigo-600 bg-indigo-50':
                                                    item.record.aft_status?.toLowerCase() ===
                                                    'present',
                                                'text-amber-600 bg-amber-50':
                                                    item.record.aft_status?.toLowerCase() ===
                                                    'late',
                                            }"
                                            class="text-xs font-bold px-2.5 py-1 rounded-lg block mx-auto w-max"
                                        >
                                            {{ item.record.check_in_aft }}
                                            <span
                                                class="text-[9px] block font-normal font-siemreap"
                                            >
                                                {{
                                                    item.record.aft_status?.toLowerCase() ===
                                                    "late"
                                                        ? "យឺត"
                                                        : "ទាន់"
                                                }}
                                            </span>
                                        </span>
                                        <span
                                            v-else
                                            class="text-gray-300 text-xs font-poppins"
                                            >-- : --</span
                                        >
                                    </td>

                                    <td
                                        class="p-4 text-center border-r border-gray-100/30"
                                    >
                                        <span
                                            v-if="item.record?.check_out_aft"
                                            class="text-xs font-bold text-gray-500 bg-gray-100 px-2.5 py-1 rounded-lg"
                                        >
                                            {{ item.record.check_out_aft }}
                                        </span>
                                        <span
                                            v-else
                                            class="text-gray-300 text-xs font-poppins"
                                            >-- : --</span
                                        >
                                    </td>

                                    <td class="p-4 text-center">
                                        <span
                                            v-if="item.record"
                                            :class="{
                                                'bg-emerald-50 text-emerald-600 border-emerald-100':
                                                    item.record.status ===
                                                    'present',
                                                'bg-amber-50 text-amber-600 border-amber-100':
                                                    item.record.status ===
                                                    'late',
                                                'bg-rose-50 text-rose-600 border-rose-100':
                                                    item.record.status ===
                                                    'absent',
                                            }"
                                            class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase border font-poppins"
                                        >
                                            {{ item.record.status }}
                                        </span>
                                        <span
                                            v-else
                                            class="text-gray-300 text-[10px] font-bold uppercase italic font-poppins"
                                        >
                                            No Record
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
