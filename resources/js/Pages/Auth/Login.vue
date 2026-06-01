<script setup>
import { onMounted } from "vue";
import Checkbox from "@/Components/Checkbox.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    username: "",
    password: "",
    remember: false,
});

onMounted(() => {
    form.reset("password");
});

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Login | Hope For Cambodian Children" />

        <div class="px-6 pb-10 md:px-12 md:pb-14">
            <div class="flex flex-col items-center mb-4">
                <img
                    src="/images/logo.png"
                    alt="Admin Logo"
                    class="w-40 h-40 object-contain"
                />
            </div>

            <div
                v-if="status"
                class="mb-6 text-sm font-medium text-green-600 bg-green-50 p-4 rounded-xl border border-green-100"
            >
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <div>
                    <InputLabel
                        for="username"
                        value="Username"
                        class="text-gray-500 font-bold ml-1 text-[12px] uppercase tracking-wider"
                    />

                    <TextInput
                        id="username"
                        type="text"
                        class="mt-2 block w-full border-gray-200 focus:border-[#01AAEB] focus:ring-[#01AAEB] rounded-xl shadow-sm transition duration-200 bg-gray-50/50 focus:bg-white h-12 px-4"
                        v-model="form.username"
                        required
                        autofocus
                        placeholder="Enter your username"
                        autocomplete="username"
                    />

                    <InputError class="mt-2" :message="form.errors.username" />
                </div>

                <div>
                    <InputLabel
                        for="password"
                        value="Password"
                        class="text-gray-500 font-bold ml-1 text-[12px] uppercase tracking-wider"
                    />

                    <TextInput
                        id="password"
                        type="password"
                        class="mt-2 block w-full border-gray-200 focus:border-[#01AAEB] focus:ring-[#01AAEB] rounded-xl shadow-sm transition duration-200 bg-gray-50/50 focus:bg-white h-12 px-4"
                        v-model="form.password"
                        required
                        placeholder="••••••••"
                        autocomplete="current-password"
                    />

                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="pt-2">
                    <PrimaryButton
                        class="w-full justify-center py-4 !bg-[#01AAEB] hover:!bg-[#0096d1] active:!bg-[#0084b8] text-white text-sm font-black rounded-xl shadow-lg shadow-blue-100 transition-all duration-300 ease-in-out transform hover:-translate-y-1 uppercase tracking-widest"
                        :class="{
                            'opacity-70 cursor-not-allowed': form.processing,
                        }"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing">Processing...</span>
                        <span v-else>Login Now</span>
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>

<style scoped>
/* បន្ថែមស្ទីលឱ្យ Input ល្អជាងមុន */
input:focus {
    box-shadow: 0 0 0 4px rgba(1, 170, 235, 0.15) !important;
}

/* បន្ថែម Animation បន្តិចពេល Hover Button */
button {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
