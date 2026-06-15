<template>
    <Head title="Scan Attendance | Hope for Cambodian Children" />
    <div
        class="min-h-screen bg-gray-100 flex flex-col items-center justify-content border-t-4 border-blue-500 p-4"
    >
        <div class="w-full max-w-md bg-white rounded-xl shadow-md p-6 mt-10">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Attendance</h1>
                <p class="text-sm text-gray-500 font-['Siemreap'] mt-1">
                    សូមស្កេន QR Code ដើម្បី Check-in/out
                </p>
            </div>

            <div
                class="relative bg-gray-950 rounded-lg overflow-hidden aspect-square border-2 border-gray-200"
            >
                <div id="reader" class="w-full h-full"></div>
            </div>

            <div class="mt-6 text-center font-['Siemreap']">
                <div
                    v-if="loading"
                    class="text-blue-600 font-medium animate-pulse"
                >
                    ⏳ កំពុងទាញយកទីតាំង GPS និងផ្ទៀងផ្ទាត់...
                </div>
                <div v-else class="text-gray-500 text-sm">
                    📷 ដាក់កាមេរ៉ាឱ្យចំរូបភាព QR Code
                </div>
            </div>

            <div
                v-if="$page.props.errors.error"
                class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-600 text-sm font-['Siemreap'] text-center"
            >
                ⚠️ {{ $page.props.errors.error }}
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import { Head, useForm } from "@inertiajs/vue3";
import { Html5QrcodeScanner } from "html5-qrcode";

const loading = ref(false);
let html5QrcodeScanner = null;

// បង្កើត Form សម្រាប់បាញ់ទៅកាន់ Laravel Backend
const form = useForm({
    latitude: null,
    longitude: null,
});

// មុខងារដំណើរការនៅពេលស្កេន QR Code ជាប់ជោគជ័យ
const onScanSuccess = (decodedText, decodedResult) => {
    // បិទកាមេរ៉ាបណ្ដោះអាសន្នសិនដើម្បីការពារកុំឱ្យវា Scan ត្រួតគ្នាពីរដង
    html5QrcodeScanner.clear();
    loading.value = true;

    // ១. ទាញយកទីតាំង GPS ពីទូរស័ព្ទរបស់បុគ្គលិក
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                form.latitude = position.coords.latitude;
                form.longitude = position.coords.longitude;

                // ២. បាញ់ទិន្នន័យ GPS ទៅកាន់ Backend តាម Route ដែលយើងបានបង្កើត
                form.post(route("attendance.store"), {
                    onSuccess: () => {
                        loading.value = false;
                        alert("🎉 កត់ត្រាវត្តមានបានជោគជ័យ!");
                        startScanner(); // បើកកាមេរ៉ាឡើងវិញ
                    },
                    onError: () => {
                        loading.value = false;
                        startScanner(); // បើកកាមេរ៉ាឡើងវិញទោះជាបរាជ័យ
                    },
                });
            },
            (error) => {
                loading.value = false;
                alert(
                    "❌ សូមបើកមុខងារ Location (GPS) នៅលើទូរស័ព្ទរបស់អ្នកជាមុនសិន!",
                );
                startScanner();
            },
            { enableHighAccuracy: true }, // បង្ខំឱ្យចាប់យកទីតាំងកម្រិតម៉ត់បំផុត
        );
    } else {
        loading.value = false;
        alert("❌ ទូរស័ព្ទ ឬ Browser របស់អ្នកមិនគាំទ្រប្រព័ន្ធ GPS ទេ។");
        startScanner();
    }
};

// មុខងារសម្រាប់បើកដំណើរការកាមេរ៉ា
const startScanner = () => {
    html5QrcodeScanner = new Html5QrcodeScanner(
        "reader",
        { fps: 10, qrbox: { width: 250, height: 250 } },
        /* verbose= */ false,
    );
    html5QrcodeScanner.render(onScanSuccess);
};

// ហៅឱ្យបើកកាមេរ៉ាភ្លាម ពេល User បើកមកដល់ទំព័រនេះ
onMounted(() => {
    startScanner();
});

// បិទកាមេរ៉ាវិញ ពេល User ចាកចេញពីទំព័រនេះទៅទំព័រផ្សេង
onBeforeUnmount(() => {
    if (html5QrcodeScanner) {
        html5QrcodeScanner.clear();
    }
});
</script>
