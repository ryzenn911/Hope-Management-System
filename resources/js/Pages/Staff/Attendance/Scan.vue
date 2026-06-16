<script setup>
import { onMounted, onBeforeUnmount, ref } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { Html5QrcodeScanner } from "html5-qrcode";

const scanResult = ref(null);
const errorMessage = ref("");
const isProcessing = ref(false);
let html5QrcodeScanner = null;

onMounted(() => {
    // ចាប់ផ្ដើមកាមេរ៉ាស្កេន
    html5QrcodeScanner = new Html5QrcodeScanner(
        "reader",
        {
            fps: 10,
            qrbox: { width: 260, height: 260 },
        },
        false,
    );

    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
});

onBeforeUnmount(() => {
    if (html5QrcodeScanner) {
        html5QrcodeScanner
            .clear()
            .catch((err) => console.error("Scanner clear error", err));
    }
});

function onScanSuccess(decodedText) {
    if (isProcessing.value) return;

    isProcessing.value = true;
    errorMessage.value = "";
    scanResult.value = "ស្កេនបានជោគជ័យ! កំពុងផ្ទៀងផ្ទាត់ GPS...";

    // ទាញយកទីតាំង GPS របស់ទូរស័ព្ទ
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                sendDataToBackend(
                    decodedText,
                    position.coords.latitude,
                    position.coords.longitude,
                );
            },
            (error) => {
                isProcessing.value = false;
                scanResult.value = null;
                errorMessage.value =
                    "សូមបើក Location (GPS) លើទូរស័ព្ទរបស់អ្នកមុននឹងស្កេនវត្តមាន។";
            },
            { enableHighAccuracy: true }, // យកទីតាំងដែលជាក់លាក់បំផុត
        );
    } else {
        isProcessing.value = false;
        scanResult.value = null;
        errorMessage.value = "Browser របស់អ្នកមិនគាំទ្រប្រព័ន្ធ GPS ឡើយ។";
    }
}

function onScanFailure(error) {
    // ទុកឱ្យវាលោតស្កេនបន្តបន្ទាប់ទៀតរហូតដល់ចំរូប QR
}

function sendDataToBackend(qrCodeText, lat, lng) {
    router.post(
        route("staff.attendance.store"),
        {
            qr_code: qrCodeText,
            latitude: lat,
            longitude: lng,
        },
        {
            onSuccess: () => {
                // ចាប់យក flash success ពី Laravel សាកល្បងបង្ហាញ
                const page = usePage();
                scanResult.value =
                    page.props.flash?.success || "ធ្វើប្រតិបត្តិការជោគជ័យ!";
                isProcessing.value = false;
            },
            onError: (errors) => {
                scanResult.value = null;
                errorMessage.value =
                    errors.error || "មានបញ្ហាកើតឡើងក្នុងការរក្សាវត្តមាន!";
                isProcessing.value = false;
            },
        },
    );
}
</script>

<template>
    <div
        class="min-h-screen bg-gray-50 flex flex-col items-center justify-center p-4"
    >
        <div
            class="w-full max-w-md bg-white rounded-3xl p-6 shadow-sm border border-gray-100 text-center"
        >
            <h2 class="text-xl font-bold text-gray-800 font-siemreap mb-1">
                ស្កេន QR Code វត្តមាន
            </h2>
            <p class="text-xs text-gray-400 font-poppins mb-6">
                Please align the QR code inside the box
            </p>

            <div
                id="reader"
                class="overflow-hidden rounded-2xl border-none"
            ></div>

            <div
                v-if="scanResult"
                class="mt-4 p-3 bg-emerald-50 text-emerald-600 rounded-xl font-siemreap text-sm font-bold"
            >
                {{ scanResult }}
            </div>
            <div
                v-if="errorMessage"
                class="mt-4 p-3 bg-rose-50 text-rose-600 rounded-xl font-siemreap text-sm font-bold"
            >
                {{ errorMessage }}
            </div>
        </div>
    </div>
</template>
