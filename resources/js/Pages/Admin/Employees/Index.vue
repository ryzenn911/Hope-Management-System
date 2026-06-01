<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Link, Head } from "@inertiajs/vue3";
import { ref, onMounted, onUnmounted, watch } from "vue";
import Swal from "sweetalert2";
import { router } from "@inertiajs/vue3";
import debounce from "lodash/debounce";
import Pagination from "@/Components/Pagination.vue";

const props = defineProps({
    employees: Object,
    filters: Object,
});

const search = ref(props.filters.search);

const goToShow = (id) => {
    router.visit(route('employees.show', id));
};

watch(
    search,
    debounce((value) => {
        router.get(
            route("employees.index"),
            { search: value },
            {
                preserveState: true,
                replace: true,
            },
        );
    }, 500),
);
const openDropdownId = ref(null);

// បើក ឬ បិទ Dropdown តាម ID
const toggleDropdown = (id) => {
    if (openDropdownId.value === id) {
        openDropdownId.value = null;
    } else {
        openDropdownId.value = id;
    }
};

const clearSearch = () => {
    search.value = "";
    handleSearch();
};

const closeDropdown = (e) => {
    if (!e.target.closest(".relative")) {
        openDropdownId.value = null;
    }
};

const confirmDelete = (id) => {
    Swal.fire({
        title: "Are you sure?",
        text: "Are you sure you want to delete this staff?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "Cancel",
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(
                route("employees.destroy", id),
                {
                    _method: "delete",
                },
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        Swal.fire({
                            title: "Deleted!",
                            text: "Staff has been deleted successfully.",
                            icon: "success",
                            timer: 1500,
                            showConfirmButton: false,
                        });
                    },
                },
            );
        }
    });
};
const formatPhone = (phone) => {
    if (!phone) return "";
    return phone.toString().replace(/(\d{3})(?=\d)/g, "$1 ");
};

onMounted(() => {
    window.addEventListener("click", closeDropdown);
});

onUnmounted(() => {
    window.removeEventListener("click", closeDropdown);
});
</script>

<template>
    <Head title="Staff | Hope for Cambodian Children" />
    <AdminLayout>
        <div class="md:px-4 px-0">
            <div class="mb-6 flex md:flex-row flex-col md:items-center items-end justify-between gap-3">
                <h1 class="md:text-2xl text-md font-bold text-gray-800 font-poppins md:flex hidden">
                    Staff List
                </h1>
                <div class="flex gap-2 md:w-1/2 w-full justify-center items-center">
                    <div class="relative w-full max-w-2xl">
                        <span
                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                        >
                            <svg
                                class="w-5 h-5 text-gray-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                />
                            </svg>
                        </span>

                        <input
                            v-model="search"
                            type="text"
                            @keyup.enter="handleSearch"
                            placeholder="Search StaffID or Name..."
                            class="block w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#01AAEB] focus:border-[#01AAEB] text-base shadow-sm transition-all"
                        />

                        <button
                            v-if="search"
                            @click="clearSearch"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 transition-colors"
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
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>
                    <button
                        @click="handleSearch"
                        class="md:px-6 px-2 py-2.5 bg-[#01AAEB] hover:bg-[#0198d1] text-white font-medium rounded-xl shadow-sm transition-all flex items-center gap-2"
                    >
                        <span>Search</span>
                    </button>
                </div>

                <Link
                    :href="route('employees.create')"
                    class="px-4  py-2 bg-[#01AAEB] text-white rounded-xl flex justify-center items-center gap-1 hover:bg-[#0198d1] transition-all"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="20"
                        height="20"
                        viewBox="0 0 24 24"
                    >
                        <path
                            fill="currentColor"
                            d="M11 13H5v-2h6V5h2v6h6v2h-6v6h-2z"
                        />
                    </svg>
                    <span class="font-medium font-poppins md:text-lg text-sm">Add Staff</span>
                </Link>
            </div>

            <div
                class="bg-white shadow-sm border border-gray-100 rounded-xl get-scrollbar overflow-x-auto md:overflow-x-visible"
            >
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead
                        class="bg-gray-200 border-b border-gray-100 text-gray-600 md:text-sm text-xs font-bold uppercase font-poppins"
                    >
                        <tr>
                            <th class="md:p-4 p-2 text-center">Staff ID</th>
                            <th class="md:p-4 p-2">Name KH</th>
                            <th class="md:p-4 p-2">Name EN</th>
                            <th class="md:p-4 p-2">Gender</th>
                            <th class="md:p-4 p-2">Date of Birth</th>
                            <th class="md:p-4 p-2">Address</th>
                            <th class="md:p-4 p-2">Position</th>
                            <th class="md:p-4 p-2">Education</th>
                            <th class="md:p-4 p-2">Phone Number</th>
                            <th class="md:p-4 p-2">Hire Date</th>
                            <th class="md:p-4 p-2">End Date</th>
                            <th class="md:p-4 p-2">Status</th>
                            <th class="md:p-4 p-2 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        <tr
                            v-for="(emp, index) in employees.data"
                            :key="emp.id"
                            @click="goToShow(emp.id)"
                            class="hover:bg-gray-100 transition-colors cursor-pointer"
                        >
                            <td
                                class="md:p-2 p-1 text-center md:text-sm text-xs text-gray-900 font-bold font-poppins"
                            >
                                {{ emp.employee_code }}
                            </td>
                            <td
                                class="md:p-2 p-1 font-medium font-siemreap text-gray-600 text-[13px]"
                            >
                                {{ emp.name_kh }}
                            </td>
                            <td class="md:p-2 p-1 uppercase text-[13px] font-poppins">
                                {{ emp.name_en }}
                            </td>
                            <td class="md:p-2 p-1 font-poppins text-[13px]">
                                {{ emp.gender }}
                            </td>
                            <td class="md:p-2 p-1 font-poppins text-[13px]">
                                {{ emp.dob }}
                            </td>
                            <td class="md:p-2 p-1 font-poppins text-[13px]">
                                {{ emp.address?.name }}
                            </td>
                            <td class="md:p-2 p-1 font-poppins text-[13px]">
                                {{ emp.position?.name || "N/A" }}
                            </td>
                            <td class="md:p-2 p-1 font-poppins text-[13px]">
                                {{ emp.education?.name }}
                            </td>
                            <td
                                class="md:p-2 p-1 text-sm font-medium text-gray-700 font-poppins"
                            >
                                {{ formatPhone(emp.phone) }}
                            </td>
                            <td class="md:p-2 p-1 font-poppins text-[13px]">
                                {{ emp.hire_date }}
                            </td>
                            <td class="md:p-2 p-1 font-poppins text-[13px]">
                                {{ emp.end_date || "_ _ _ _" }}
                            </td>
                            <td class="md:p-2 p-1 text-center">
                                <span
                                    :class="{
                                        'bg-green-100 text-green-700':
                                            emp.status === 'Active',
                                        'bg-red-100 text-red-700':
                                            emp.status === 'Resigned',
                                    }"
                                    class="px-3 py-1 rounded-full text-xs font-medium font-poppins"
                                >
                                    {{ emp.status || "Active" }}
                                </span>
                            </td>
                            <td class="md:p-2 p-1 text-center">
                                <div class="relative inline-block text-left" @click.stop>
                                    <button
                                        @click.stop="toggleDropdown(emp.id)"
                                        class="p-2 hover:bg-gray-100 rounded-full transition-colors focus:outline-none"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="20"
                                            height="20"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="text-gray-600"
                                        >
                                            <circle cx="12" cy="12" r="1" />
                                            <circle cx="12" cy="5" r="1" />
                                            <circle cx="12" cy="19" r="1" />
                                        </svg>
                                    </button>

                                    <div
                                        v-if="openDropdownId === emp.id"
                                        class="absolute right-0 mt-2 w-32 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden font-siemreap"
                                    >
                                        <div class="flex flex-col py-1">
                                            <Link
                                                :href="
                                                    route(
                                                        'employees.show',
                                                        emp.id,
                                                    )
                                                "
                                                class="flex items-center gap-3 px-4 py-2 text-sm text-blue-600 hover:bg-blue-50 transition-colors"
                                            >
                                                <svg
                                                    class="w-4 h-4"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                                    />
                                                </svg>
                                                <span class="font-poppins"
                                                    >View</span
                                                >
                                            </Link>

                                            <Link
                                                :href="
                                                    route(
                                                        'employees.edit',
                                                        emp.id,
                                                    )
                                                "
                                                class="flex items-center gap-3 px-4 py-2 text-sm text-yellow-600 hover:bg-yellow-50 transition-colors"
                                            >
                                                <svg
                                                    class="w-4 h-4"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                                    />
                                                </svg>
                                                <span class="font-poppins"
                                                    >Edit</span
                                                >
                                            </Link>

                                            <button
                                                @click="confirmDelete(emp.id)"
                                                class="flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors w-full text-left font-siemreap"
                                            >
                                                <svg
                                                    class="w-4 h-4"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                    />
                                                </svg>
                                                <span class="font-poppins"
                                                    >Delete</span
                                                >
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="employees.data.length === 0">
                            <td
                                colspan="13"
                                class="p-10 text-center text-gray-400 italic"
                            >
                                No staff found.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mt-1">
                <Pagination :links="employees.links" />
            </div>
        </div>
    </AdminLayout>
</template>
