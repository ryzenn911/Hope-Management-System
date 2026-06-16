<script setup>
import { onBeforeUnmount, ref, onMounted } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { Html5Qrcode } from "html5-qrcode";
import Swal from "sweetalert2"; // ហៅចូល SweetAlert2

const scanResult = ref(null);
const errorMessage = ref("");
const isProcessing = ref(false);
const hasCameraPermission = ref(false);
let html5Qrcode = null;

// បង្ហាញការ Loading បែបប្រព័ន្ធលោតពេញអេក្រង់
function showLoading(message) {
    Swal.fire({
        title: message,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        },
    });
}

// ទំព័ររត់ឡើង ឱ្យវាកាត់ទៅ Request សុំបើកកាមេរ៉ាអូតូភ្លាម
onMounted(() => {
    requestCameraAndStart();
});

async function requestCameraAndStart() {
    errorMessage.value = "";
    try {
        html5Qrcode = new Html5Qrcode("reader");
        const cameras = await Html5Qrcode.getCameras();

        if (cameras && cameras.length > 0) {
            hasCameraPermission.value = true;

            const backCamera = cameras.find(
                (cam) =>
                    cam.label.toLowerCase().includes("back") ||
                    cam.label.toLowerCase().includes("environment"),
            );
            const cameraId = backCamera ? backCamera.id : cameras[0].id;

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
            errorMessage.value = "រកមិនឃើញឧបករណ៍កាមេរ៉ានៅលើឧបករណ៍របស់អ្នកឡើយ។";
        }
    } catch (error) {
        hasCameraPermission.value = false;
        errorMessage.value =
            "ការបើកកាមេរ៉ាត្រូវបានបដិសេធ! សូមបើកសិទ្ធិ (Allow Camera) ក្នុង Setting នៃ Browser។";
    }
}

onBeforeUnmount(() => {
    if (html5Qrcode && html5Qrcode.isScanning) {
        html5Qrcode
            .stop()
            .then(() => {
                html5Qrcode.clear();
            })
            .catch((err) => console.error(err));
    }
});

function onScanSuccess(decodedText) {
    if (isProcessing.value) return;
    isProcessing.value = true;
    if (html5Qrcode && html5Qrcode.isScanning) {
        html5Qrcode.stop().catch((err) => console.error(err));
    }

    showLoading("កំពុងផ្ទៀងផ្ទាត់ GPS និងទិន្នន័យ...");

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
                Swal.fire({
                    icon: "error",
                    title: "បរាជ័យលើប្រព័ន្ធ GPS",
                    text: "សូមបើក Location (GPS) លើទូរស័ព្ទរបស់អ្នក រួចព្យាយាមម្តងទៀត។",
                    confirmButtonText: "យល់ព្រម",
                }).then(() => {
                    // បើខុស GPS ឱ្យវាបើកកាមេរ៉ាដំណើរការឡើងវិញ
                    requestCameraAndStart();
                });
            },
            { enableHighAccuracy: true, timeout: 10000 },
        );
    } else {
        isProcessing.value = false;
        Swal.fire({
            icon: "error",
            title: "មិនគាំទ្រប្រព័ន្ធ GPS",
            text: "Browser របស់អ្នកមិនគាំទ្រប្រព័ន្ធទីតាំងឡើយ។",
            confirmButtonText: "យល់ព្រម",
        });
    }
}

function onScanFailure(error) {
    // បន្តឱ្យស្កេនរហូតដល់ចំកូដត្រឹមត្រូវ
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
                Swal.close(); // បិទប្រអប់ Loading

                // SweetAlert ករណីស្កេនបានជោគជ័យ
                Swal.fire({
                    icon: "success",
                    title: "កត់ត្រាវត្តមានជោគជ័យ",
                    text:
                        page.props.flash?.success ||
                        "ទិន្នន័យវត្តមានរបស់អ្នកត្រូវបានរក្សាទុក។",
                    timer: 2500,
                    showConfirmButton: false,
                }).then(() => {
                    // រុញត្រឡប់ទៅកាន់ទំព័រ Dashboard វិញអូតូ
                    router.visit(route("staff.dashboard"));
                });

                isProcessing.value = false;
            },
            onError: (errors) => {
                Swal.close(); // បិទប្រអប់ Loading

                // SweetAlert ករណីមានកំហុស ឬទិន្នន័យយឺត/ខុសកូដ
                Swal.fire({
                    icon: "error",
                    title: "មិនអាចកត់ត្រាវត្តមានបានទេ",
                    text:
                        errors.error ||
                        "កូដខុស ឬការតភ្ជាប់អ៊ីនធឺណិតមានបញ្ហាយឺតខ្លាំង!",
                    confirmButtonText: "ព្យាយាមម្តងទៀត",
                }).then(() => {
                    isProcessing.value = false;
                    // អនុញ្ញាតឱ្យដំណើរការកាមេរ៉ាស្កេនឡើងវិញ
                    requestCameraAndStart();
                });
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
                class="overflow-hidden rounded-2xl border-none mx-auto"
            ></div>

            <div
                v-if="errorMessage"
                class="mt-4 p-3 bg-rose-50 text-rose-600 rounded-xl font-siemreap text-sm font-bold"
            >
                {{ errorMessage }}
            </div>
        </div>
    </div>
</template>
