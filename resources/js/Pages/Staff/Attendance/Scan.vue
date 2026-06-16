<script setup>
import { onBeforeUnmount, ref } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { Html5Qrcode } from "html5-qrcode"; // ប្តូរមកប្រើ Html5Qrcode វិញដើម្បីស្រួល Control កាមេរ៉ាដោយខ្លួនឯង

const scanResult = ref(null);
const errorMessage = ref("");
const isProcessing = ref(false);
const hasCameraPermission = ref(false); // សម្រាប់ឆែកមើលថាបានសិទ្ធិឬនៅ
let html5Qrcode = null;

// Function សម្រាប់ទាមទារសិទ្ធិបើកកាមេរ៉ា និងចាប់ផ្ដើមស្កេន
async function requestCameraAndStart() {
    errorMessage.value = "";

    try {
        // បង្កើត Instance របស់ Scanner ចូលទៅក្នុង tag id="reader"
        html5Qrcode = new Html5Qrcode("reader");

        // លោតសួរ Request Camera Permission ពី User ផ្ទាល់តែម្ដង
        const cameras = await Html5Qrcode.getCameras();

        if (cameras && cameras.length > 0) {
            hasCameraPermission.value = true; // បើកផ្ទាំងកាមេរ៉ាលើ UI

            // ជ្រើសរើសយកកាមេរ៉ាក្រោយ (Back Camera) របស់ទូរស័ព្ទជាចម្បង
            const backCamera = cameras.find(
                (cam) =>
                    cam.label.toLowerCase().includes("back") ||
                    cam.label.toLowerCase().includes("environment"),
            );
            const cameraId = backCamera ? backCamera.id : cameras[0].id;

            // ចាប់ផ្ដើមដំណើរការស្កេន
            await html5Qrcode.start(
                cameraId,
                {
                    fps: 10,
                    qrbox: { width: 260, height: 260 },
                },
                onScanSuccess,
                onScanFailure,
            );
        } else {
            errorMessage.value =
                "រកមិនឃើញឧបករណ៍កាមេរ៉ានៅលើទូរស័ព្ទ ឬកុំព្យូទ័ររបស់អ្នកឡើយ។";
        }
    } catch (error) {
        hasCameraPermission.value = false;
        errorMessage.value =
            "ការបើកកាមេរ៉ាត្រូវបានបដិសេធ! សូមចូលទៅកាន់ Setting របស់ Browser ដើម្បីបើកសិទ្ធិ (Allow Camera)។";
        console.error("Camera access denied or error:", error);
    }
}

onBeforeUnmount(() => {
    if (html5Qrcode && html5Qrcode.isScanning) {
        html5Qrcode
            .stop()
            .then(() => {
                html5Qrcode.clear();
            })
            .catch((err) => console.error("Failed to stop scanner", err));
    }
});

function onScanSuccess(decodedText) {
    if (isProcessing.value) return;

    isProcessing.value = true;
    errorMessage.value = "";
    scanResult.value = "ស្កេនបានជោគជ័យ! កំពុងផ្ទៀងផ្ទាត់ GPS...";

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
            { enableHighAccuracy: true },
        );
    } else {
        isProcessing.value = false;
        scanResult.value = null;
        errorMessage.value = "Browser របស់អ្នកមិនគាំទ្រប្រព័ន្ធ GPS ឡើយ។";
    }
}

function onScanFailure(error) {
    // បណ្តោយឱ្យវាស្កេនបន្តរហូតដល់ចំ QR Code
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
                v-if="!hasCameraPermission"
                class="py-12 border-2 border-dashed border-gray-200 rounded-2xl flex flex-col items-center justify-center bg-gray-50"
            >
                <div class="p-4 bg-blue-50 rounded-full text-blue-500 mb-4">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="w-10 h-10"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z"
                        />
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z"
                        />
                    </svg>
                </div>
                <button
                    @click="requestCameraAndStart"
                    class="px-6 py-2.5 bg-[#01AAEB] text-white font-semibold rounded-xl shadow-md shadow-blue-100 hover:bg-blue-600 transition-all font-siemreap text-sm"
                >
                    ចុចទីនេះដើម្បីបើកកាមេរ៉ាស្កេន
                </button>
            </div>

            <div
                v-show="hasCameraPermission"
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
