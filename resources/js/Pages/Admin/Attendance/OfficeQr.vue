<script setup>
defineProps({
    qrCodeUrl: String,
    qrValue: String,
});

// Function សម្រាប់បើក Window ថ្មីដើម្បី Print តែរូប QR Code
function printQr() {
    const printWindow = window.open("", "_blank");
    printWindow.document.write(`
        <html>
            <head>
                <title>Print Office QR Code</title>
                <style>
                    body { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100vh; margin: 0; font-family: sans-serif; }
                    img { width: 400px; height: 400px; }
                    h2 { margin-top: 20px; color: #333; }
                </style>
            </head>
            <body>
                <img src="${props.qrCodeUrl}" />
                <h2>QR Code វត្តមានក្រុមហ៊ុន (បោះពុម្ពម្តងប្រើបានរហូត)</h2>
                <script>
                    window.onload = function() { window.print(); window.close(); }
                <\/script>
            </body>
        </html>
    `);
    printWindow.document.close();
}
</script>

<template>
    <div class="min-h-screen bg-gray-50 p-6 flex items-center justify-center">
        <div
            class="p-8 bg-white rounded-3xl max-w-sm w-full text-center shadow-sm border border-gray-100"
        >
            <h3 class="text-lg font-bold mb-2 font-siemreap text-gray-800">
                QR Code វត្តមានបុគ្គលិក
            </h3>
            <p class="text-xs text-gray-400 mb-6 font-poppins">
                Print this QR code and paste it in the office
            </p>

            <div
                class="flex justify-center p-4 bg-gray-50 rounded-2xl mb-4 border border-dashed border-gray-200"
            >
                <img
                    :src="qrCodeUrl"
                    alt="Office QR Code"
                    class="w-64 h-64 mix-blend-multiply"
                />
            </div>

            <div
                class="bg-blue-50 text-blue-600 rounded-xl p-3 text-xs font-mono mb-6 break-all"
            >
                Value: {{ qrValue }}
            </div>

            <button
                @click="printQr"
                class="w-full py-3 bg-[#01AAEB] hover:bg-blue-600 text-white font-semibold rounded-xl shadow-md shadow-blue-100 transition-all font-siemreap text-sm"
            >
                បោះពុម្ព (Print QR Code)
            </button>
        </div>
    </div>
</template>
