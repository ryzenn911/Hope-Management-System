<script setup>
import { Head, Link } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
const props = defineProps({
    employee: Object,
});
</script>

<template>
    <Head title="Employee Details" />
    <AdminLayout>
        <div class="md:mr-10 mr-0 ml-0 md:ml-10 md:mt-5 mt-0 md:mx-20 mx-0">
            <div
                class="w-full mx-auto bg-white rounded-lg shadow-sm overflow-hidden"
            >
                <div
                    class="p-6 border-b flex justify-between items-center bg-white"
                >
                    <h2
                        class="md:text-xl text-sm font-poppins text-gray-800 font-bold uppercase"
                    >
                        Staff Information
                    </h2>
                    <Link
                        :href="route('employees.index')"
                        class="px-4 py-2 bg-[#01AAEB] text-white rounded-xl md:flex hidden justify-center items-center gap-1 cursor-pointer"
                    >
                        <span
                            ><svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                            >
                                <rect width="24" height="24" fill="none" />
                                <path
                                    fill="currentColor"
                                    d="M17.51 3.87L15.73 2.1L5.84 12l9.9 9.9l1.77-1.77L9.38 12z"
                                /></svg
                        ></span>
                        <span class="font-poppins">Back</span>
                    </Link>
                </div>

                <div class="flex flex-col md:flex-row">
                    <div
                        class="w-full md:w-1/3 p-8 border-r bg-white flex flex-col items-center"
                    >
                        <div
                            class="md:w-40 md:h-40 w-28 h-28 bg-[#01AAEB] rounded-full uppercase flex items-center justify-center text-white md:text-7xl text-3xl font-bold mb-4 shadow-inner"
                        >
                            {{ employee.name_en.charAt(0) }}
                        </div>
                        <h3
                            class="text-lg font-bold text-gray-900 font-siemreap"
                        >
                            {{ employee.name_kh }}
                        </h3>
                        <p
                            class="text-gray-800 text-sm mb-4 font-poppins uppercase"
                        >
                            {{ employee.name_en }}
                        </p>

                        <span
                            :class="{
                                'bg-green-100 text-green-700':
                                    employee.status === 'Active',
                                'bg-red-100 text-red-700':
                                    employee.status === 'Resigned',
                            }"
                            class="px-4 py-1 rounded-full text-xs font-semibold"
                        >
                            {{ employee.status }}
                        </span>

                        <div
                            class="mt-6 border-t border-gray-100 pt-6 flex flex-col items-center"
                        >
                            <div class="w-full max-w-5xl space-y-3">
                                <div class="flex items-center text-sm">
                                    <label class="w-32 text-gray-500 text-left"
                                        >Staff ID:</label
                                    >
                                    <div class="text-gray-900 font-bold">
                                        {{ employee.employee_code }}
                                    </div>
                                </div>

                                <div class="flex items-center text-sm">
                                    <label class="w-32 text-gray-500 text-left"
                                        >Position:</label
                                    >
                                    <div
                                        class="text-gray-900 font-bold font-siemreap"
                                    >
                                        {{ employee.position?.name }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="w-full md:w-2/3 p-8 space-y-8">
                        <section>
                            <h4
                                class="text-[#01AAEB] font-bold text-sm uppercase tracking-wider mb-4 border-b pb-2"
                            >
                                Personal Info
                            </h4>
                            <div
                                class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm"
                            >
                                <div>
                                    <p class="text-gray-500">Gender</p>
                                    <p class="font-medium text-gray-900">
                                        {{ employee.gender }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Data of Birth</p>
                                    <p class="font-medium text-gray-900">
                                        {{ employee.dob }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Phone Number</p>
                                    <p class="font-medium text-gray-900">
                                        {{ employee.phone }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Address</p>
                                    <p class="font-medium text-gray-900">
                                        {{ employee.address?.name }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Education Level</p>
                                    <p class="font-medium text-gray-900">
                                        {{ employee.education?.name }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Marital Status</p>
                                    <p
                                        class="font-medium text-gray-900 capitalize"
                                    >
                                        {{ employee.marital_status || "---" }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-500">NSSF Number</p>
                                    <p class="font-medium text-gray-900">
                                        {{ employee.nssf_number || "---" }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Labor Book</p>
                                    <p class="font-medium text-gray-900">
                                        {{ employee.labor_book || "---" }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Family Number</p>
                                    <p class="font-medium text-gray-900">
                                        {{ employee.family_number || "---" }}
                                    </p>
                                </div>
                            </div>
                        </section>

                        <section>
                            <h4
                                class="text-[#01AAEB] font-bold text-sm uppercase tracking-wider mb-4 border-b pb-2"
                            >
                                Staff Info
                            </h4>
                            <div
                                class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm"
                            >
                                <div>
                                    <p class="text-gray-500">Hire Date</p>
                                    <p class="font-medium text-gray-900">
                                        {{ employee.hire_date }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-500">End Date</p>
                                    <p class="font-medium text-gray-900">
                                        {{ employee.end_date || "---" }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Username</p>
                                    <p class="font-medium text-gray-900">
                                        {{ employee.user?.username }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Email</p>
                                    <p class="font-medium text-gray-900">
                                        {{ employee.user?.email }}
                                    </p>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
