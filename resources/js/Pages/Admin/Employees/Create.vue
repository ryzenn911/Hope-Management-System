<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, Link, useForm, usePage } from "@inertiajs/vue3";
import { ref, onMounted, watch } from "vue";
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.css";
import Swal from "sweetalert2";

const props = defineProps({
    addresses: Array,
    positions: Array,
    educations: Array,
});

const isOtherPosition = ref(false);
const isOtherEducation = ref(false);

const form = useForm({
    // Account Info
    username: "",
    email: "",
    password: "",
    // Personal Info
    employee_code: "",
    name_kh: "",
    name_en: "",
    gender: "",
    marital_status: "",
    dob: "",
    address_id: "",
    position_id: "",
    other_position_name: "",
    education_id: "",
    other_education_name: "",
    phone: "",
    hire_date: "",
    nssf_number: "",
    labor_book: "",
    family_number: "",
});

const handlePositionChange = () => {
    form.clearErrors("position_id");
    if (form.position_id === "other") {
        isOtherPosition.value = true;
    } else {
        isOtherPosition.value = false;
        form.other_position_name = "";
    }
};

const handleEducationChange = () => {
    form.clearErrors("education_id");
    if (form.education_id === "other") {
        isOtherEducation.value = true;
    } else {
        isOtherEducation.value = false;
        form.other_education_name = "";
    }
};

const formatPhone01 = (event) => {
    form.clearErrors("phone");
    let value = event.target.value.replace(/\D/g, "");
    if (value.length > 10) value = value.substring(0, 10);
    const formatted = value.match(/.{1,3}/g)?.join(" ") || "";
    form.phone = formatted;
};

const formatPhone02 = (event) => {
    form.clearErrors("phone");
    let value = event.target.value.replace(/\D/g, "");
    if (value.length > 10) value = value.substring(0, 10);
    const formatted = value.match(/.{1,3}/g)?.join(" ") || "";
    form.phone = formatted;
};

const dobInput = ref(null);
const hireDateRef = ref(null);
let fpEnd = null;

onMounted(() => {
    flatpickr(hireDateRef.value, {
        dateFormat: "Y-m-d",
        altFormat: "d-m-Y",
        altInput: true,
        allowInput: true,
        onChange: (selectedDates, dateStr) => {
            form.hire_date = dateStr;
            if (fpEnd) {
                fpEnd.set("minDate", dateStr);
            }
        },
    });

    // ៣. បង្កើត DOB
    flatpickr(dobInput.value, {
        dateFormat: "Y-m-d",
        altFormat: "d-m-Y",
        altInput: true,
        allowInput: true,
        maxDate: "today",
        onChange: (selectedDates, dateStr) => {
            form.dob = dateStr;
        },
    });
});

const page = usePage();

watch(
    () => page.props.flash?.success,
    (message) => {
        console.log("Flash Message:", message);
        if (message) {
            Swal.fire({
                title: "Success!",
                text: message,
                icon: "success",
                confirmButtonColor: "#01AAEB",
                timer: 3000,
            });
        }
    },
    { immediate: true },
);

const submit = () => {
    form.post(route("employees.store"), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire({
                title: "Success!",
                text: "Staff created successfully.",
                icon: "success",
                confirmButtonColor: "#01AAEB",
                timer: 3000,
            });
        },
        onError: (errors) => {
            if (errors.error) {
                Swal.fire({
                    title: "មានបញ្ហា!",
                    text: errors.error,
                    icon: "error",
                    confirmButtonColor: "#01AAEB",
                });
            }
        },
    });
};
</script>

<template>
    <Head title="Create Staff | Hope for Cambodian Children" />
    <AdminLayout>
        <div
            class="flex items-center justify-between mb-6 md:mx-5 mx-0 md:my-5 my-0 md:mr-10 mr-0 md:ml-12 ml-0"
        >
            <h1 class="md:text-2xl text-md font-bold font-poppins">
                Create Staff
            </h1>

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
        <div class="max-w-8xl mx-auto md:mr-10 mr-0 ml-0 md:ml-10">
            <form
                @submit.prevent="submit"
                class="flex flex-col md:flex-row gap-6"
            >
                <div
                    class="bg-white md:p-8 p-4 rounded-2xl shadow-sm w-full md:w-1/2 flex flex-col"
                >
                    <h3
                        class="text-black mb-6 md:text-md text-sm font-bold md:pb-10 pb-2 underline decoration-[#01AAEB] uppercase font-poppins"
                    >
                        Account Information
                    </h3>
                    <div
                        class="grid grid-cols-1 gap-4 md:mx-5 mx-2 font-poppins"
                    >
                        <div class="flex flex-col md:flex-row md:items-center">
                            <label class="w-60 text-gray-700">Username*:</label>
                            <div class="w-full flex flex-col">
                                <input
                                    autocomplete="off"
                                    v-model="form.username"
                                    type="text"
                                    @input="form.clearErrors('username')"
                                    :class="{
                                        'border-red-500': form.errors.username,
                                    }"
                                    class="mt-1 block w-full border border-gray-300 leading-7 outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-200 rounded-md p-2 text-sm"
                                />
                                <p
                                    v-if="form.errors.username"
                                    class="text-red-500 text-xs mt-1 font-poppins"
                                >
                                    {{ form.errors.username }}
                                </p>
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row md:items-center">
                            <label class="w-60 text-gray-700">Email*:</label>
                            <div class="w-full flex flex-col">
                                <input
                                    v-model="form.email"
                                    type="email"
                                    @input="form.clearErrors('email')"
                                    :class="{
                                        'border-red-500': form.errors.email,
                                    }"
                                    class="mt-1 block w-full border border-gray-300 leading-7 outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-200 rounded-md p-2 text-sm"
                                />
                                <p
                                    v-if="form.errors.email"
                                    class="text-red-500 text-xs mt-1 font-poppins"
                                >
                                    {{ form.errors.email }}
                                </p>
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row md:items-center">
                            <label class="w-60 text-gray-700">Password*:</label>
                            <div class="w-full flex flex-col">
                                <input
                                    autocomplete="off"
                                    v-model="form.password"
                                    :class="{
                                        'border-red-500': form.errors.password,
                                    }"
                                    type="password"
                                    class="mt-1 block w-full border border-gray-300 leading-7 outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-200 rounded-md p-2 text-sm"
                                    @input="form.clearErrors('password')"
                                />
                                <p
                                    v-if="form.errors.password"
                                    class="text-red-500 text-xs mt-1 font-poppins"
                                >
                                    {{ form.errors.password }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    class="bg-white md:p-8 p-4 rounded-2xl shadow-sm w-full md:w-3/4 font-poppins"
                >
                    <h3
                        class="text-black mb-6 md:text-md text-sm font-bold md:pb-10 pb-2 underline decoration-[#01AAEB] uppercase"
                    >
                        Personal Information
                    </h3>
                    <div class="grid grid-cols-1 gap-4 md:mx-20 mx-2">
                        <div class="flex flex-col md:flex-row md:items-center">
                            <label class="w-72 text-gray-700">StaffID*:</label>
                            <div class="w-full flex flex-col">
                                <input
                                    v-model="form.employee_code"
                                    @input="form.clearErrors('employee_code')"
                                    type="text"
                                    :class="{
                                        'border-red-500':
                                            form.errors.employee_code,
                                    }"
                                    class="mt-1 block w-full border border-gray-300 leading-7 outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-200 rounded-md p-2 text-sm"
                                />
                                <span
                                    v-if="form.errors.employee_code"
                                    class="text-red-500 text-xs mt-1 font-poppins"
                                >
                                    {{ form.errors.employee_code }}
                                </span>
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row md:items-center">
                            <label class="block w-72 text-gray-700"
                                >Khmer Name*:</label
                            >
                            <div class="w-full flex flex-col">
                                <input
                                    v-model="form.name_kh"
                                    type="text"
                                    :class="{
                                        'border-red-500': form.errors.name_kh,
                                    }"
                                    @input="form.clearErrors('name_kh')"
                                    class="mt-1 block w-full font-siemreap outline-none leading-7 transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-200 border border-gray-300 rounded-md p-2 text-sm"
                                />
                                <span
                                    v-if="form.errors.name_kh"
                                    class="text-red-500 text-xs mt-1 font-poppins"
                                >
                                    {{ form.errors.name_kh }}
                                </span>
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row md:items-center">
                            <label class="block w-72 text-gray-700"
                                >English Name*:</label
                            >
                            <div class="w-full flex flex-col">
                                <input
                                    v-model="form.name_en"
                                    type="text"
                                    :class="{
                                        'border-red-500': form.errors.name_en,
                                    }"
                                    @input="form.clearErrors('name_en')"
                                    class="mt-1 block w-full uppercase outline-none leading-7 transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-200 border border-gray-300 rounded-md p-2 text-sm"
                                />
                                <span
                                    v-if="form.errors.name_en"
                                    class="text-red-500 text-xs mt-1 font-poppins"
                                >
                                    {{ form.errors.name_en }}
                                </span>
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row md:items-center">
                            <label class="w-72 text-gray-700">Gender*:</label>
                            <div class="w-full flex flex-col">
                                <select
                                    v-model="form.gender"
                                    :class="{
                                        'text-gray-400': !form.gender,
                                        'text-gray-900': form.gender,
                                        'border-red-500': form.errors.gender,
                                    }"
                                    @change="form.clearErrors('gender')"
                                    class="mt-1 block w-full border border-gray-300 outline-none leading-7 transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-200 rounded-md p-2 text-sm cursor-pointer"
                                >
                                    <option value="" disabled selected>
                                        Select Gender
                                    </option>
                                    <option value="Male" class="text-gray-900">
                                        Male
                                    </option>
                                    <option
                                        value="Female"
                                        class="text-gray-900"
                                    >
                                        Female
                                    </option>
                                </select>
                                <span
                                    v-if="form.errors.gender"
                                    class="text-red-500 text-xs mt-1 font-poppins"
                                >
                                    {{ form.errors.gender }}
                                </span>
                            </div>
                        </div>
                        <div
                            class="flex flex-col md:flex-row justify-between items-center"
                        >
                            <label class="block text-sm text-gray-700 w-72"
                                >Marital Status*:</label
                            >
                            <div class="flex flex-col w-full">
                                <select
                                    v-model="form.marital_status"
                                    :class="[
                                        'mt-1 block w-full leading-7 border border-gray-300 outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-200 rounded-md px-2 py-2 text-sm cursor-pointer',
                                        form.marital_status === ''
                                            ? 'text-gray-400'
                                            : 'text-gray-900',
                                    ]"
                                >
                                    <option value="" disabled>
                                        Marital Status
                                    </option>
                                    <option
                                        class="text-gray-900"
                                        value="single"
                                    >
                                        Single
                                    </option>
                                    <option
                                        class="text-gray-900"
                                        value="married"
                                    >
                                        Married
                                    </option>
                                    <option
                                        class="text-gray-900"
                                        value="divorced"
                                    >
                                        Divorced
                                    </option>
                                    <option
                                        class="text-gray-900"
                                        value="widowed"
                                    >
                                        Widowed
                                    </option>
                                </select>

                                <p
                                    v-if="form.errors.marital_status"
                                    class="mt-2 text-[11px] text-red-600 leading-none"
                                >
                                    {{ form.errors.marital_status }}
                                </p>
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row md:items-center">
                            <label class="w-72 text-gray-700"
                                >Date of Birth*:</label
                            >
                            <div class="w-full flex flex-col">
                                <div :class="{ 'date-error': form.errors.dob }">
                                    <input
                                        ref="dobInput"
                                        @input="form.clearErrors('dob')"
                                        v-model="form.dob"
                                        type="text"
                                        class="mt-1 block w-full border border-gray-300 leading-7 outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-200 rounded-md p-2 text-sm cursor-pointer bg-white"
                                    />
                                </div>
                                <span
                                    v-if="form.errors.dob"
                                    class="text-red-500 text-xs mt-1 font-poppins"
                                >
                                    {{ form.errors.dob }}
                                </span>
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row md:items-center">
                            <label class="w-72 text-gray-700">Address*:</label>
                            <div class="w-full flex flex-col">
                                <select
                                    v-model="form.address_id"
                                    :class="{
                                        'text-gray-400': !form.address_id,
                                        'text-gray-900': form.address_id,
                                        'border-red-500':
                                            form.errors.address_id,
                                    }"
                                    class="mt-1 block w-full border border-gray-300 outline-none leading-7 transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-200 rounded-md p-2 text-sm cursor-pointer"
                                    @change="form.clearErrors('address_id')"
                                >
                                    <option value="" disabled selected>
                                        Select Address
                                    </option>

                                    <option
                                        v-for="addr in addresses"
                                        :key="addr.id"
                                        :value="addr.id"
                                        class="text-gray-900"
                                    >
                                        {{ addr.name }}
                                    </option>
                                </select>

                                <div
                                    v-if="form.errors.address_id"
                                    class="text-red-500 text-xs mt-1 font-poppins"
                                >
                                    {{ form.errors.address_id }}
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row md:items-center">
                            <label class="w-72 text-gray-700">Position*:</label>
                            <div class="w-full flex flex-col">
                                <div v-if="!isOtherPosition">
                                    <select
                                        v-model="form.position_id"
                                        @change="handlePositionChange"
                                        :class="{
                                            'text-gray-400': !form.position_id,
                                            'text-gray-900': form.position_id,
                                            'border-red-500':
                                                form.errors.position_id,
                                        }"
                                        class="mt-1 block w-full border border-gray-300 outline-none leading-7 transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-200 rounded-md p-2 text-sm cursor-pointer"
                                    >
                                        <option value="" disabled selected>
                                            Select a position
                                        </option>
                                        <option
                                            v-for="pos in positions"
                                            :key="pos.id"
                                            :value="pos.id"
                                            class="text-gray-900 font-siemreap"
                                        >
                                            {{ pos.name }}
                                        </option>
                                        <option
                                            value="other"
                                            class="text-gray-900"
                                        >
                                            Other
                                        </option>
                                    </select>
                                </div>

                                <div v-else class="relative flex items-center">
                                    <input
                                        v-model="form.other_position_name"
                                        type="text"
                                        :class="{
                                            'border-red-500':
                                                form.errors.position_id,
                                        }"
                                        placeholder="Input the new position"
                                        class="mt-1 block w-full border border-blue-400 outline-none leading-7 transition-all focus:ring-2 focus:ring-blue-200 rounded-md p-2 pr-10 text-sm"
                                        autofocus
                                    />
                                    <button
                                        type="button"
                                        @click="
                                            isOtherPosition = false;
                                            form.position_id = '';
                                        "
                                        class="absolute right-3 top-1/2 -translate-y-1/2 mt-0.5 text-gray-400 hover:text-red-500 transition-colors"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"
                                            />
                                        </svg>
                                    </button>
                                </div>

                                <div
                                    v-if="form.errors.position_id"
                                    class="text-red-500 text-xs mt-1 font-poppins"
                                >
                                    {{ form.errors.position_id }}
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row md:items-center">
                            <label class="w-72 text-gray-700"
                                >Education Level*:</label
                            >
                            <div class="w-full flex flex-col">
                                <div v-if="!isOtherEducation">
                                    <select
                                        v-model="form.education_id"
                                        @change="handleEducationChange"
                                        :class="{
                                            'text-gray-400': !form.education_id,
                                            'text-gray-900': form.education_id,
                                            'border-red-500':
                                                form.errors.education_id,
                                        }"
                                        class="mt-1 block w-full border border-gray-300 outline-none leading-7 transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-200 rounded-md p-2 text-sm cursor-pointer"
                                    >
                                        <option value="" disabled selected>
                                            Select Education
                                        </option>
                                        <option
                                            v-for="edu in educations"
                                            :key="edu.id"
                                            :value="edu.id"
                                            class="text-gray-900"
                                        >
                                            {{ edu.name }}
                                        </option>
                                        <option
                                            value="other"
                                            class="text-gray-900"
                                        >
                                            Other
                                        </option>
                                    </select>
                                </div>

                                <div v-else class="relative flex items-center">
                                    <input
                                        v-model="form.other_education_name"
                                        type="text"
                                        :class="{
                                            'border-red-500':
                                                form.errors.education_id,
                                        }"
                                        placeholder="Input the new Education Level"
                                        class="mt-1 block w-full border border-blue-400 outline-none leading-7 transition-all focus:ring-2 focus:ring-blue-200 rounded-md p-2 pr-10 text-sm"
                                        autofocus
                                    />
                                    <button
                                        type="button"
                                        @click="
                                            isOtherEducation = false;
                                            form.education_id = '';
                                        "
                                        class="absolute right-3 top-1/2 -translate-y-1/2 mt-0.5 text-gray-400 hover:text-red-500"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"
                                            />
                                        </svg>
                                    </button>
                                </div>
                                <div
                                    v-if="form.errors.education_id"
                                    class="text-red-500 text-xs mt-1 font-poppins"
                                >
                                    {{ form.errors.education_id }}
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row md:items-center">
                            <label class="block w-72 text-gray-700"
                                >Phone Number*:</label
                            >
                            <div class="w-full flex flex-col">
                                <input
                                    type="text"
                                    v-model="form.phone"
                                    @input="formatPhone01"
                                    :class="{
                                        'border-red-500': form.errors.phone,
                                    }"
                                    placeholder="012 345 678"
                                    class="mt-1 block w-full outline-none leading-7 transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-200 border border-gray-300 rounded-md p-2 text-sm"
                                />
                                <span
                                    v-if="form.errors.phone"
                                    class="text-red-500 text-xs mt-1 font-poppins"
                                >
                                    {{ form.errors.phone }}
                                </span>
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row md:items-center">
                            <label class="w-72 text-gray-700"
                                >Hire Date*:</label
                            >
                            <div class="w-full flex flex-col">
                                <div :class="{ 'date-error': form.errors.dob }">
                                    <input
                                        type="text"
                                        ref="hireDateRef"
                                        @input="form.clearErrors('hire_date')"
                                        v-model="form.hire_date"
                                        class="mt-1 block w-full border border-gray-300 outline-none leading-7 transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-200 rounded-md p-2 text-sm"
                                    />
                                </div>

                                <span
                                    v-if="form.errors.hire_date"
                                    class="text-red-500 text-xs mt-1 font-poppins"
                                >
                                    {{ form.errors.hire_date }}
                                </span>
                            </div>
                        </div>
                        <div
                            class="flex justify-between items-center flex-col md:flex-row"
                        >
                            <label class="block text-sm text-gray-700 w-72"
                                >NSSF*:</label
                            >
                            <div class="flex flex-col w-full">
                                <input
                                    type="text"
                                    v-model="form.nssf_number"
                                    placeholder="NSSF Number"
                                    :class="[
                                        'mt-1 block w-full border leading-7 outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-200 rounded-md p-2 text-sm',
                                        form.errors.nssf_number
                                            ? 'border-red-500'
                                            : 'border-gray-300',
                                    ]"
                                />
                            </div>
                        </div>

                        <div
                            class="flex justify-between items-center flex-col md:flex-row"
                        >
                            <label class="block text-sm text-gray-700 w-72"
                                >Labor Book*:</label
                            >
                            <div class="flex flex-col w-full">
                                <input
                                    type="text"
                                    v-model="form.labor_book"
                                    placeholder="Labor Book Number"
                                    :class="[
                                        'mt-1 block w-full border leading-7 outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-200 rounded-md p-2 text-sm',
                                        form.errors.labor_book
                                            ? 'border-red-500'
                                            : 'border-gray-300',
                                    ]"
                                />
                            </div>
                        </div>

                        <div
                            class="flex justify-between items-center flex-col md:flex-row"
                        >
                            <label class="block text-sm text-gray-700 w-72"
                                >Family Phone Number*:</label
                            >
                            <div class="flex flex-col w-full">
                                <input
                                    type="text"
                                    v-model="form.family_number"
                                    @input="formatPhone02"
                                    placeholder="012 345 678"
                                    :class="[
                                        'mt-1 block w-full border leading-7 outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-200 rounded-md p-2 text-sm',
                                        form.errors.family_number
                                            ? 'border-red-500'
                                            : 'border-gray-300',
                                    ]"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="md:pt-10 pt-5 md:pl-20 pl-2">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="bg-[#01AAEB] text-white px-6 py-2 rounded-xl text-md hover:scale-105 transition-all shadow-lg shadow-blue-100 active:scale-95 flex items-center gap-2 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <svg
                                v-if="!form.processing"
                                xmlns="http://www.w3.org/2000/svg"
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    fill="#fff"
                                    d="M21 7v12q0 .825-.587 1.413T19 21H5q-.825 0-1.412-.587T3 19V5q0-.825.588-1.412T5 3h12zm-6.875 10.125Q15 16.25 15 15t-.875-2.125T12 12t-2.125.875T9 15t.875 2.125T12 18t2.125-.875M6 10h9V6H6z"
                                />
                            </svg>
                            <svg
                                v-else
                                class="animate-spin h-5 w-5 text-white"
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

                            <span>{{
                                form.processing ? "Processing Save..." : "Save"
                            }}</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
<style scoped>
/* បង្ខំឱ្យ Input របស់ Flatpickr ចេញពណ៌ក្រហម */
.date-error :deep(input) {
    border-color: #ef4444 !important;
}

.date-error :deep(input:focus) {
    --tw-ring-color: rgba(239, 68, 68, 0.2) !important;
    border-color: #ef4444 !important;
}
</style>
