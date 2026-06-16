<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm, Link, Head } from "@inertiajs/vue3";
import { watch, onMounted } from "vue";
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.css";

const form = useForm({
    leave_type: "",
    start_date: "",
    end_date: "",
    total_days: 0,
    reason: "",
});

onMounted(() => {
    // សម្រាប់ Start Date
    flatpickr(".flatpickr-start", {
        altInput: true,
        altFormat: "d-m-Y",
        dateFormat: "Y-m-d",
        onChange: (selectedDates, dateStr) => {
            form.start_date = dateStr;
        },
    });

    // សម្រាប់ End Date
    flatpickr(".flatpickr-end", {
        altInput: true,
        altFormat: "d-m-Y",
        dateFormat: "Y-m-d",
        onChange: (selectedDates, dateStr) => {
            form.end_date = dateStr;
        },
    });
});

const calculateDays = () => {
    if (form.start_date && form.end_date) {
        let start = new Date(form.start_date);
        let end = new Date(form.end_date);
        let count = 0;

        let current = new Date(start);
        while (current <= end) {
            const dayOfWeek = current.getDay();
            if (dayOfWeek !== 0 && dayOfWeek !== 6) {
                count++;
            }
            current.setDate(current.getDate() + 1);
        }

        form.total_days = count;
    } else {
        form.total_days = 0;
    }
};

watch(() => [form.start_date, form.end_date], calculateDays);

const submit = () => {
    form.post(route("staff.leaves.store"));
};
</script>

<template>
    <Head title="Request Leave | Hope for Cambodia Children" />
    <AuthenticatedLayout>
        <div class="p-6 max-w-lg mx-auto">
            <div class="flex items-center mb-6">
                <Link
                    href="/staff/dashboard"
                    class="mr-4 text-gray-400 p-2 hover:bg-gray-100 rounded-full transition-all"
                >
                    <i class="fas fa-arrow-left text-xl"></i>
                </Link>
                <h1 class="text-xl font-bold text-gray-800 font-poppins">
                    Leaves Request
                </h1>
            </div>

            <form
                @submit.prevent="submit"
                class="space-y-5 bg-white p-6 rounded-2xl shadow-sm border border-gray-100"
            >
                <div>
                    <label
                        class="block text-xs font-bold text-gray-400 uppercase mb-2"
                        >Leave Type</label
                    >
                    <select
                        v-model="form.leave_type"
                        class="w-full border-gray-200 rounded-xl focus:ring-[#01AAEB] focus:border-[#01AAEB]"
                    >
                        <option value="">Select Leave Type</option>
                        <option value="Sick Leave">Sick Leave</option>
                        <option value="Annual Leave">Annual Leave</option>
                        <option value="Special Leave">Special Leave</option>
                        <option value="Maternity Leave">Maternity Leave</option>
                    </select>
                    <div
                        v-if="form.errors.leave_type"
                        class="text-red-500 text-xs mt-1"
                    >
                        {{ form.errors.leave_type }}
                    </div>
                </div>

                <div class="space-y-5">
                    <div>
                        <label
                            class="block text-xs font-bold text-gray-400 uppercase mb-2"
                            >Start Date</label
                        >
                        <div class="relative">
                            <input
                                type="text"
                                class="flatpickr-start w-full border-gray-200 rounded-xl focus:ring-[#01AAEB] focus:border-[#01AAEB] bg-white"
                                placeholder="Select Start Date"
                            />
                            <i
                                class="far fa-calendar absolute right-4 top-3 text-gray-400 pointer-events-none"
                            ></i>
                        </div>
                        <div
                            v-if="form.errors.start_date"
                            class="text-red-500 text-xs mt-1"
                        >
                            {{ form.errors.start_date }}
                        </div>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-bold text-gray-400 uppercase mb-2"
                            >End Date</label
                        >
                        <div class="relative">
                            <input
                                type="text"
                                class="flatpickr-end w-full border-gray-200 rounded-xl focus:ring-[#01AAEB] focus:border-[#01AAEB] bg-white"
                                placeholder="Select End Date"
                            />
                            <i
                                class="far fa-calendar absolute right-4 top-3 text-gray-400 pointer-events-none"
                            ></i>
                        </div>
                        <div
                            v-if="form.errors.end_date"
                            class="text-red-500 text-xs mt-1"
                        >
                            {{ form.errors.end_date }}
                        </div>
                    </div>
                </div>

                <div
                    class="bg-gray-50 p-4 rounded-xl border border-dashed border-gray-200"
                >
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500 font-medium"
                            >Total Days:</span
                        >
                        <span class="text-xl font-bold text-[#F44336]"
                            >{{ form.total_days }} Days</span
                        >
                    </div>
                    <input type="hidden" v-model="form.total_days" />
                </div>

                <div>
                    <label
                        class="block text-xs font-bold text-gray-400 uppercase mb-2"
                        >Reason</label
                    >
                    <textarea
                        v-model="form.reason"
                        rows="3"
                        class="w-full border-gray-200 rounded-xl focus:ring-[#01AAEB] focus:border-[#01AAEB]"
                        placeholder="Explain the reason for your leave..."
                    ></textarea>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full bg-[#01AAEB] hover:bg-[#13b3f3] text-white font-bold py-4 rounded-xl shadow-md active:scale-95 transition-all disabled:opacity-50"
                >
                    {{ form.processing ? "Submitting..." : "Submit Request" }}
                </button>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
