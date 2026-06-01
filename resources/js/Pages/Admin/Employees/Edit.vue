<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref, onMounted, watch } from "vue";
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.css";
import Swal from "sweetalert2";

const props = defineProps({
    employee: Object,
    addresses: Array,
    positions: Array,
    educations: Array,
});

const isOtherPosition = ref(false);
const isOtherEducation = ref(false);

const formatPhoneNumber1 = (val) => {
    if (!val) return "";
    let value = val.toString().replace(/\D/g, "");
    if (value.length > 10) value = value.substring(0, 10);
    return value.match(/.{1,3}/g)?.join(" ") || value;
};

const formatPhoneNumber2 = (val) => {
    if (!val) return "";
    let value = val.toString().replace(/\D/g, "");
    if (value.length > 10) value = value.substring(0, 10);
    return value.match(/.{1,3}/g)?.join(" ") || value;
};

const form = useForm({
    _method: "put",
    username: props.employee.user?.username || "",
    email: props.employee.user?.email || "",
    password: "********",
    employee_code: props.employee.employee_code || "",
    name_kh: props.employee.name_kh || "",
    name_en: props.employee.name_en || "",
    gender: props.employee.gender || "",
    dob: props.employee.dob || "",
    marital_status: props.employee.marital_status || "",
    nssf_number: props.employee.nssf_number || "",
    labor_book: props.employee.labor_book || "",
    family_number: formatPhoneNumber2(props.employee.family_number),
    address_id: props.employee.address_id || "",
    position_id: props.employee.position_id || "",
    education_id: props.employee.education_id || "",
    other_position_name: "",
    other_education_name: "",
    phone: formatPhoneNumber1(props.employee.phone),
    hire_date: props.employee.hire_date || "",
    end_date: props.employee.end_date || "",
    status: props.employee.status || "Active",
});

const handlePositionChange = () => {
    isOtherPosition.value = form.position_id === "other";
    if (!isOtherPosition.value) form.other_position_name = "";
};

// Function សម្រាប់ឆែកការប្តូរ Education
const handleEducationChange = () => {
    isOtherEducation.value = form.education_id === "other";
    if (!isOtherEducation.value) form.other_education_name = "";
};

const formatPhone2 = (event) => {
    form.family_number = formatPhoneNumber1(event.target.value);
};

const formatPhone1 = (event) => {
    form.phone = formatPhoneNumber2(event.target.value);
};

watch(
    () => form.end_date,
    (newDate) => {
        form.status = newDate ? "Resigned" : "Active";
    },
);

const dobInput = ref(null);
const hireDateRef = ref(null);
const endDateRef = ref(null); // បន្ថែមថ្មី

onMounted(() => {
    // Hire Date
    flatpickr(hireDateRef.value, {
        dateFormat: "Y-m-d",
        altFormat: "d-m-Y",
        altInput: true,
        defaultDate: form.hire_date,
        onChange: (selectedDates, dateStr) => {
            form.hire_date = dateStr;
        },
    });

    // Date of Birth
    flatpickr(dobInput.value, {
        dateFormat: "Y-m-d",
        altFormat: "d-m-Y",
        altInput: true,
        defaultDate: form.dob,
        maxDate: "today",
        onChange: (selectedDates, dateStr) => {
            form.dob = dateStr;
        },
    });

    // End Date (បន្ថែមថ្មី)
    flatpickr(endDateRef.value, {
        dateFormat: "Y-m-d",
        altFormat: "d-m-Y",
        altInput: true,
        defaultDate: form.end_date,
        onChange: (selectedDates, dateStr) => {
            form.end_date = dateStr;
            form.status = dateStr ? "Resigned" : "Active";
        },
    });
});

const Alert = (icon, title, text) => {
    Swal.fire({
        icon: icon,
        title: title,
        text: text,
        position: "center",
        showConfirmButton: true,
        confirmButtonColor: "#01AAEB",
        timer: 5000,
    });
};

const submit = () => {
    // លក្ខខណ្ឌ Check: សម្រាប់ Edit យើងមិនបាច់តម្រូវឱ្យបំពេញ Password ទេ
    if (
        !form.employee_code ||
        !form.name_kh ||
        !form.name_en ||
        !form.username ||
        !form.email ||
        !form.gender ||
        !form.dob ||
        !form.address_id ||
        !form.position_id ||
        !form.education_id ||
        !form.hire_date
    ) {
        Alert(
            "error",
            "Missing Information",
            "Please fill in all required fields!",
        );
        return;
    }

    // ប្រើ PUT សម្រាប់ Update
    form.post(route("employees.update", props.employee.id), {
        preserveScroll: true,
        onSuccess: () => {
            Alert("success", "Updated!", "Staff information has been updated.");
        },
        onError: () => {
            Alert("error", "Failed", "Check your data again.");
        },
    });
};
</script>

<template>
    <Head title="Edit Staff | Hope for Cambodian Children" />
    <AdminLayout>
        <div
            class="flex items-center justify-between mb-6 mx-5 md:my-5 my-0 md:mr-10 mr-0 md:ml-12 ml-0"
        >
            <h1 class="md:text-2xl text-sm font-bold font-poppins">
                Edit Staff
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
        <div class="max-w-8xl mx-auto md:mr-10 mr-0 md:ml-10 ml-0">
            <form
                action=""
                @submit.prevent="submit"
                class="flex flex-col md:flex-row gap-6"
            >
                <div
                    class="bg-white md:p-8 p-4 rounded-2xl shadow-sm w-full md:w-1/2 flex flex-col font-poppins"
                >
                    <h3
                        class="text-black md:text-md text-sm font-bold md:pb-10 pb-5 underline decoration-[#01AAEB] uppercase"
                    >
                        Account Information
                    </h3>
                    <div class="grid grid-cols-1 gap-4 md:mx-5 mx-0">
                        <div class="flex flex-col md:flex-row md:items-center">
                            <label class="w-60 text-gray-700">Username*:</label>
                            <div class="w-full flex flex-col">
                                <input
                                    autocomplete="off"
                                    v-model="form.username"
                                    type="text"
                                    class="mt-1 block w-full border border-gray-300 leading-7 outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-200 rounded-md p-2 text-sm"
                                />
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row md:items-center">
                            <label class="w-60 text-gray-700">Email*:</label>
                            <div class="w-full flex flex-col">
                                <input
                                    v-model="form.email"
                                    type="email"
                                    class="mt-1 block w-full border border-gray-300 leading-7 outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-200 rounded-md p-2 text-sm"
                                />
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row md:items-center">
                            <label class="w-60 text-gray-700">Password*:</label>
                            <div class="w-full flex flex-col">
                                <input
                                    autocomplete="off"
                                    v-model="form.password"
                                    type="password"
                                    class="mt-1 block w-full border border-gray-300 leading-7 outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-200 rounded-md p-2 text-sm"
                                />
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    class="bg-white md:p-8 p-4 rounded-2xl shadow-sm w-full md:w-3/4 font-poppins"
                >
                    <h3
                        class="text-black text-md font-bold md:pb-10 pb-5 underline decoration-[#01AAEB] uppercase"
                    >
                        Personal Information
                    </h3>
                    <div class="grid grid-cols-1 gap-4 md:mx-20 mx-0">
                        <div class="flex flex-col md:flex-row md:items-center">
                            <label class="w-72 text-gray-700">StaffID*:</label>
                            <div class="w-full flex flex-col">
                                <input
                                    v-model="form.employee_code"
                                    type="text"
                                    readonly=""
                                    class="mt-1 block w-full border border-gray-300 leading-7 outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-200 rounded-md p-2 text-sm"
                                />
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
                                    class="mt-1 block w-full outline-none font-siemreap leading-7 transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-200 border border-gray-300 rounded-md p-2 text-sm"
                                />
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
                                    class="mt-1 block w-full uppercase outline-none leading-7 transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-200 border border-gray-300 rounded-md p-2 text-sm"
                                />
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row md:items-center">
                            <label class="w-72 text-gray-700">Gender*:</label>
                            <div class="w-full flex flex-col">
                                <select
                                    v-model="form.gender"
                                    class="mt-1 block w-full border border-gray-300 outline-none leading-7 transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-200 rounded-md p-2 text-sm cursor-pointer"
                                >
                                    <option value="" disabled>
                                        Select Gender
                                    </option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row md:items-center">
                            <label class="w-72 text-gray-700"
                                >Marital Status*:</label
                            >
                            <div class="w-full flex flex-col">
                                <select
                                    v-model="form.marital_status"
                                    class="mt-1 block w-full border border-gray-300 outline-none leading-7 transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-200 rounded-md p-2 text-sm cursor-pointer"
                                >
                                    <option value="" disabled>
                                        Select Marital Status
                                    </option>
                                    <option value="single">Single</option>
                                    <option value="married">Married</option>
                                    <option value="divorced">Divorced</option>
                                    <option value="widowed">Widowed</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row md:items-center">
                            <label class="w-72 text-gray-700"
                                >Date of Birth*:</label
                            >
                            <div class="w-full flex flex-col">
                                <input
                                    ref="dobInput"
                                    type="text"
                                    class="mt-1 block w-full border border-gray-300 leading-7 outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-200 rounded-md p-2 text-sm cursor-pointer bg-white"
                                />
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row md:items-center">
                            <label class="w-72 text-gray-700">Address*:</label>
                            <div class="w-full flex flex-col">
                                <select
                                    v-model="form.address_id"
                                    class="mt-1 block w-full border border-gray-300 outline-none leading-7 transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-200 rounded-md p-2 text-sm cursor-pointer"
                                >
                                    <option value="" disabled selected>
                                        Select a province
                                    </option>

                                    <option
                                        v-for="addr in addresses"
                                        :key="addr.id"
                                        :value="addr.id"
                                    >
                                        {{ addr.name }}
                                    </option>
                                </select>
                                <div
                                    v-if="form.errors.address_id"
                                    class="text-red-500 text-xs mt-1"
                                >
                                    {{ form.errors.address_id }}
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row md:items-center">
                            <label class="w-72 text-gray-700">Position*:</label>
                            <div class="w-full flex flex-col">
                                <div v-if="!isOtherPosition" class="relative">
                                    <select
                                        v-model="form.position_id"
                                        @change="handlePositionChange"
                                        :class="{
                                            'text-gray-400': !form.position_id,
                                            'text-gray-900': form.position_id,
                                        }"
                                        class="mt-1 block w-full border border-gray-300 outline-none rounded-md p-2 text-sm cursor-pointer"
                                    >
                                        <option value="" disabled>
                                            Select a position
                                        </option>
                                        <option
                                            v-for="pos in positions"
                                            :key="pos.id"
                                            :value="pos.id"
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
                                        placeholder="Input the new position"
                                        class="mt-1 block w-full border border-blue-400 outline-none rounded-md p-2 pr-10 text-sm"
                                        autofocus
                                    />
                                    <button
                                        type="button"
                                        @click="
                                            isOtherPosition = false;
                                            form.position_id =
                                                props.employee.position_id;
                                        "
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500"
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
                                        }"
                                        class="mt-1 block w-full border border-gray-300 outline-none rounded-md p-2 text-sm cursor-pointer"
                                    >
                                        <option value="" disabled>
                                            Select Education
                                        </option>
                                        <option
                                            v-for="edu in educations"
                                            :key="edu.id"
                                            :value="edu.id"
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
                                        placeholder="Input the new Education Level"
                                        class="mt-1 block w-full border border-blue-400 outline-none rounded-md p-2 pr-10 text-sm"
                                        autofocus
                                    />
                                    <button
                                        type="button"
                                        @click="
                                            isOtherEducation = false;
                                            form.education_id =
                                                props.employee.education_id;
                                        "
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500"
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
                                    @input="formatPhone1"
                                    placeholder="012 345 678"
                                    class="mt-1 block w-full outline-none leading-7 transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-200 border border-gray-300 rounded-md p-2 text-sm"
                                />
                                <span
                                    v-if="form.errors.phone"
                                    class="text-red-500 text-xs mt-1"
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
                                <input
                                    type="text"
                                    ref="hireDateRef"
                                    v-model="form.hire_date"
                                    class="mt-1 block w-full border border-gray-300 outline-none leading-7 transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-200 rounded-md p-2 text-sm"
                                />
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row md:items-center">
                            <label class="w-72 text-gray-700">End Date:</label>
                            <div class="w-full">
                                <input
                                    ref="endDateRef"
                                    type="date"
                                    v-model="form.end_date"
                                    class="mt-1 block w-full border border-gray-300 outline-none leading-7 transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-200 rounded-md p-2 text-sm"
                                />
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row md:items-center">
                            <label class="w-72 text-gray-700"
                                >NSSF Number:</label
                            >
                            <div class="w-full flex flex-col">
                                <input
                                    v-model="form.nssf_number"
                                    type="text"
                                    class="mt-1 block w-full border border-gray-300 leading-7 outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-200 rounded-md p-2 text-sm"
                                />
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row md:items-center">
                            <label class="w-72 text-gray-700"
                                >Labor Book:</label
                            >
                            <div class="w-full flex flex-col">
                                <input
                                    v-model="form.labor_book"
                                    type="text"
                                    class="mt-1 block w-full border border-gray-300 leading-7 outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-200 rounded-md p-2 text-sm"
                                />
                            </div>
                        </div>

                        <div class="flex flex-col md:flex-row md:items-center">
                            <label class="w-72 text-gray-700"
                                >Family Phone Number:</label
                            >
                            <div class="w-full flex flex-col">
                                <input
                                    type="text"
                                    v-model="form.family_number"
                                    @input="formatPhone2"
                                    placeholder="012 345 678"
                                    class="mt-1 block w-full outline-none leading-7 transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-200 border border-gray-300 rounded-md p-2 text-sm"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="md:pt-10 pt-5 md:pl-20 pl-0">
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
                                form.processing ? "Proceessing..." : "Update"
                            }}</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
