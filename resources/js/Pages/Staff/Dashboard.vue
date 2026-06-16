<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, usePage, router } from "@inertiajs/vue3";
import { Html5Qrcode } from "html5-qrcode";
import { computed, ref } from "vue";

// 💡 កែប្រែ Props ឱ្យត្រូវទៅតាមទិន្នន័យដែលបោះមកពី Laravel Controller ថ្មី
const props = defineProps({
    todayRecord: Object, // ទទួលយក Object វត្តមានថ្ងៃនេះ (ឬ null បើមិនទាន់ស្កេន)
});

const isCheckingPermission = ref(false);

/**
 * មុខងារសម្រាប់ឆែកសិទ្ធិកាមេរ៉ា និងបញ្ជូនទៅកាន់ទំព័រស្កេន QR Code
 */
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

// ទាញយកព័ត៌មាន User ដែលបាន Login ជាប់
const page = usePage();
const user = computed(() => page.props.auth.user);
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
                    class="bg-white rounded-3xl border border-gray-100 shadow-sm mx-5 mb-10 overflow-hidden"
                >
                    <div
                        class="flex items-center justify-between px-6 py-4 border-b border-gray-50 bg-gray-50/50"
                    >
                        <div class="flex items-center space-x-2">
                            <div class="p-2 bg-blue-50 rounded-xl">
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
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                            </div>
                            <div>
                                <h3
                                    class="text-gray-800 font-bold font-siemreap text-base leading-tight"
                                >
                                    វត្តមានថ្ងៃនេះ
                                </h3>
                                <p
                                    class="text-[11px] text-gray-400 font-poppins"
                                >
                                    Today's Attendance
                                </p>
                            </div>
                        </div>
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-[#01AAEB] font-poppins animate-pulse"
                        >
                            Today
                        </span>
                    </div>

                    <div class="p-6">
                        <div
                            v-if="props.todayRecord"
                            class="grid grid-cols-1 md:grid-cols-2 gap-4"
                        >
                            <div
                                class="p-4 rounded-2xl bg-slate-50 border border-slate-100/50 flex flex-col justify-between gap-4"
                            >
                                <div class="flex items-center justify-between">
                                    <span
                                        class="text-sm font-bold text-blue-600 font-siemreap"
                                        >វេនព្រឹក (Morning)</span
                                    >
                                    <span
                                        v-if="props.todayRecord.check_in_morn"
                                        :class="{
                                            'text-emerald-600 bg-emerald-50 border-emerald-100':
                                                props.todayRecord.morn_status?.toLowerCase() ===
                                                'present',
                                            'text-amber-600 bg-amber-50 border-amber-100':
                                                props.todayRecord.morn_status?.toLowerCase() ===
                                                'late',
                                        }"
                                        class="text-[10px] font-bold px-2.5 py-0.5 rounded-full border uppercase font-poppins"
                                    >
                                        {{
                                            props.todayRecord.morn_status?.toLowerCase() ===
                                            "late"
                                                ? "យឺត / Late"
                                                : "ទាន់ពេល / Present"
                                        }}
                                    </span>
                                    <span
                                        v-else
                                        class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-500 border border-rose-100 font-siemreap"
                                        >អវត្តមាន</span
                                    >
                                </div>

                                <div class="grid grid-cols-2 gap-2 text-center">
                                    <div
                                        class="p-2 bg-white rounded-xl border border-gray-100"
                                    >
                                        <span
                                            class="block text-[10px] text-gray-400 uppercase font-poppins"
                                            >Morn In</span
                                        >
                                        <span
                                            class="text-base font-bold text-gray-700 font-poppins"
                                            >{{
                                                props.todayRecord
                                                    .check_in_morn || "-- : --"
                                            }}</span
                                        >
                                    </div>
                                    <div
                                        class="p-2 bg-white rounded-xl border border-gray-100"
                                    >
                                        <span
                                            class="block text-[10px] text-gray-400 uppercase font-poppins"
                                            >Morn Out</span
                                        >
                                        <span
                                            class="text-base font-bold text-gray-700 font-poppins"
                                            >{{
                                                props.todayRecord
                                                    .check_out_morn || "-- : --"
                                            }}</span
                                        >
                                    </div>
                                </div>
                            </div>

                            <div
                                class="p-4 rounded-2xl bg-slate-50 border border-slate-100/50 flex flex-col justify-between gap-4"
                            >
                                <div class="flex items-center justify-between">
                                    <span
                                        class="text-sm font-bold text-indigo-600 font-siemreap"
                                        >វេនល្ងាច (Afternoon)</span
                                    >
                                    <span
                                        v-if="props.todayRecord.check_in_aft"
                                        :class="{
                                            'text-indigo-600 bg-indigo-50 border-indigo-100':
                                                props.todayRecord.aft_status?.toLowerCase() ===
                                                'present',
                                            'text-amber-600 bg-amber-50 border-amber-100':
                                                props.todayRecord.aft_status?.toLowerCase() ===
                                                'late',
                                        }"
                                        class="text-[10px] font-bold px-2.5 py-0.5 rounded-full border uppercase font-poppins"
                                    >
                                        {{
                                            props.todayRecord.aft_status?.toLowerCase() ===
                                            "late"
                                                ? "យឺត / Late"
                                                : "ទាន់ពេល / Present"
                                        }}
                                    </span>
                                    <span
                                        v-else
                                        class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-500 border border-rose-100 font-siemreap"
                                        >អវត្តមាន</span
                                    >
                                </div>

                                <div class="grid grid-cols-2 gap-2 text-center">
                                    <div
                                        class="p-2 bg-white rounded-xl border border-gray-100"
                                    >
                                        <span
                                            class="block text-[10px] text-gray-400 uppercase font-poppins"
                                            >Aft In</span
                                        >
                                        <span
                                            class="text-base font-bold text-gray-700 font-poppins"
                                            >{{
                                                props.todayRecord
                                                    .check_in_aft || "-- : --"
                                            }}</span
                                        >
                                    </div>
                                    <div
                                        class="p-2 bg-white rounded-xl border border-gray-100"
                                    >
                                        <span
                                            class="block text-[10px] text-gray-400 uppercase font-poppins"
                                            >Aft Out</span
                                        >
                                        <span
                                            class="text-base font-bold text-gray-700 font-poppins"
                                            >{{
                                                props.todayRecord
                                                    .check_out_aft || "-- : --"
                                            }}</span
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-else class="text-center py-8">
                            <svg
                                class="w-12 h-12 text-gray-300 mx-auto mb-2"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.5"
                                    d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                            <p
                                class="text-gray-400 font-bold text-sm font-siemreap"
                            >
                                មិនទាន់មានទិន្នន័យវត្តមាននៅឡើយទេ
                            </p>
                            <p class="text-[11px] text-gray-400 font-poppins">
                                No attendance record found for today.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
