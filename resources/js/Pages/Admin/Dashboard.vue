<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head } from "@inertiajs/vue3";

defineProps({
    activeStaffCount: Number,
    totalStaff: Number,
});
</script>

<template>
    <Head title="Dashboard | Hope for Cambodian Children" />
    <AdminLayout>
        <div
            class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 md:gap-6 gap-2 mb-8 md:mt-5 mt-0 md:mx-7 mx-1"
        >
            <div
                class="bg-[#8BC34A] md:p-7 p-4 rounded-2xl shadow-sm flex items-center justify-start transition transform hover:scale-105 md:h-full h-20"
            >
                <div>
                    <p
                        class="md:text-sm text-xs font-medium text-white uppercase tracking-widest md:mb-1 mb-0 font-poppins"
                    >
                        Total Staff
                    </p>
                    <p class="md:text-5xl text-2xl font-bold text-white">
                        {{ activeStaffCount }}
                    </p>
                </div>
            </div>

            <div
                class="bg-[#01AAEB] md:p-7 p-4 rounded-2xl shadow-sm flex items-center justify-start transition transform hover:scale-105 md:h-full h-20"
            >
                <div>
                    <p
                        class="md:text-sm text-xs font-medium text-white uppercase tracking-widest md:mb-1 mb-0 font-poppins"
                    >
                        Present Today
                    </p>
                    <p class="md:text-5xl text-2xl font-bold text-white">0</p>
                </div>
            </div>

            <div
                class="bg-[#F44336] md:p-7 p-4 rounded-2xl shadow-sm flex items-center justify-start transition transform hover:scale-105 md:h-full h-20"
            >
                <div>
                    <p
                        class="md:text-sm text-xs font-medium text-white uppercase tracking-widest md:mb-1 mb-0 font-poppins"
                    >
                        Absent Today
                    </p>
                    <p class="md:text-5xl text-2xl font-bold text-white">0</p>
                </div>
            </div>

            <div
                class="bg-[#FFC107] md:p-7 p-4 rounded-2xl shadow-sm flex items-center justify-start transition transform hover:scale-105 md:h-full h-20"
            >
                <div>
                    <p
                        class="md:text-sm text-xs font-medium text-white uppercase tracking-widest md:mb-1 mb-0 font-poppins"
                    >
                        On Leave Today
                    </p>
                    <p class="md:text-5xl text-2xl font-bold text-white">0</p>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
