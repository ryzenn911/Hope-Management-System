<script setup>
import { ref, onMounted } from "vue";
import { useForm } from "@inertiajs/vue3";
import L from "leaflet";
import "leaflet/dist/leaflet.css";

const props = defineProps({
    location: Object,
    employees: Array,
});

// ១. Form សម្រាប់ Location
const locationForm = useForm({
    lat: props.location?.lat ?? 11.5564,
    lng: props.location?.lng ?? 104.9282,
    radius: props.location?.radius ?? 50,
});

// ២. Form សម្រាប់ Staff Shift Setting
const shiftForm = useForm({
    employee_id: "",
    session_count: 2, // ជម្រើស ១ ឬ ២ វេន
    s1_in: "07:00",
    s1_out: "11:00",
    s2_in: "13:00",
    s2_out: "17:00",
});

const mapContainer = ref(null);
let map, marker;

onMounted(() => {
    map = L.map(mapContainer.value).setView(
        [locationForm.lat, locationForm.lng],
        15,
    );
    L.tileLayer("http://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}", {
        maxZoom: 20,
        subdomains: ["mt0", "mt1", "mt2", "mt3"],
    }).addTo(map);
    marker = L.marker([locationForm.lat, locationForm.lng], {
        draggable: true,
    }).addTo(map);

    marker.on("dragend", (e) => {
        const { lat, lng } = e.target.getLatLng();
        locationForm.lat = lat.toFixed(8);
        locationForm.lng = lng.toFixed(8);
    });
});

const saveLocation = () => {
    locationForm.post(route("attendance.updateLocation"), {
        preserveScroll: true,
    });
};

const saveShift = () => {
    // បងនឹងបង្កើត Route នេះនៅជំហានបន្ទាប់
    shiftForm.post(route("attendance.saveShift"), {
        preserveScroll: true,
        onSuccess: () => alert("កំណត់ម៉ោងបានជោគជ័យ!"),
    });
};
</script>

<template>
    <div class="space-y-6 font-poppins">
        <div
            class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden"
        >
            <div class="grid grid-cols-1 lg:grid-cols-12">
                <div
                    class="lg:col-span-4 bg-gray-50/50 p-5 border-r border-gray-100"
                >
                    <div class="flex items-center gap-2 mb-4">
                        <div
                            class="p-1.5 bg-[#01AAEB]/10 rounded-lg text-[#01AAEB]"
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
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                                />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                                />
                            </svg>
                        </div>
                        <h3 class="font-bold text-gray-700">Office Location</h3>
                    </div>

                    <div
                        ref="mapContainer"
                        class="h-64 rounded-xl border border-gray-200 z-0 mb-4 shadow-inner"
                    ></div>

                    <div class="space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label
                                    class="text-[10px] font-black text-gray-400 uppercase tracking-tighter"
                                    >Latitude</label
                                >
                                <input
                                    v-model="locationForm.lat"
                                    readonly
                                    class="w-full p-2 bg-white border border-gray-200 rounded-lg text-xs font-mono text-gray-600"
                                />
                            </div>
                            <div>
                                <label
                                    class="text-[10px] font-black text-gray-400 uppercase tracking-tighter"
                                    >Longitude</label
                                >
                                <input
                                    v-model="locationForm.lng"
                                    readonly
                                    class="w-full p-2 bg-white border border-gray-200 rounded-lg text-xs font-mono text-gray-600"
                                />
                            </div>
                        </div>
                        <div>
                            <label
                                class="text-[10px] font-black text-gray-400 uppercase tracking-tighter"
                                >Radius (Meters)</label
                            >
                            <div class="relative">
                                <input
                                    v-model="locationForm.radius"
                                    type="number"
                                    class="w-full p-2 border border-gray-200 rounded-lg text-sm pr-10 focus:ring-2 focus:ring-[#01AAEB]/20"
                                />
                                <span
                                    class="absolute right-3 top-2 text-xs text-gray-400 font-bold"
                                    >M</span
                                >
                            </div>
                        </div>
                        <button
                            @click="saveLocation"
                            class="w-full bg-[#01AAEB] text-white py-2.5 rounded-lg font-bold text-xs uppercase tracking-widest transition hover:bg-[#0198d1] active:scale-95 shadow-sm"
                        >
                            Update Location
                        </button>
                    </div>
                </div>

                <div class="lg:col-span-8 p-6">
                    <div class="flex items-center gap-2 mb-6">
                        <div
                            class="p-1.5 bg-indigo-50 rounded-lg text-indigo-600"
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
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </div>
                        <h3 class="font-bold text-gray-700">
                            Shift Configuration
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-6">
                            <div class="group">
                                <label
                                    class="block text-[11px] font-black text-gray-400 uppercase tracking-wider mb-2"
                                    >Target Employee</label
                                >
                                <select
                                    v-model="shiftForm.employee_id"
                                    class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 focus:bg-white focus:ring-2 focus:ring-indigo-500/10 transition-all cursor-pointer"
                                >
                                    <option value="">-- Select --</option>
                                    <option
                                        v-for="emp in employees"
                                        :key="emp.id"
                                        :value="emp.id"
                                        class="uppercase"
                                    >
                                        {{ emp.name_en }}
                                    </option>
                                </select>
                            </div>

                            <div class="group">
                                <label
                                    class="block text-[11px] font-black text-gray-400 uppercase tracking-wider mb-2"
                                    >Work Duration</label
                                >
                                <div
                                    class="flex gap-2 p-1 bg-gray-100 rounded-xl"
                                >
                                    <button
                                        @click="shiftForm.session_count = 1"
                                        type="button"
                                        :class="
                                            shiftForm.session_count == 1
                                                ? 'bg-[#01AAEB] text-white shadow-sm'
                                                : 'text-gray-500 hover:text-gray-700'
                                        "
                                        class="flex-1 py-2 text-[11px] font-black rounded-lg transition-all"
                                    >
                                        HALF DAY
                                    </button>
                                    <button
                                        @click="shiftForm.session_count = 2"
                                        type="button"
                                        :class="
                                            shiftForm.session_count == 2
                                                ? 'bg-[#01AAEB] text-white shadow-sm'
                                                : 'text-gray-500 hover:text-gray-700'
                                        "
                                        class="flex-1 py-2 text-[11px] font-black rounded-lg transition-all"
                                    >
                                        FULL DAY
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div
                                class="p-4 border border-gray-100 rounded-xl bg-gray-50/30"
                            >
                                <p
                                    class="text-[11px] font-black text-[#01AAEB] uppercase mb-3 border-b border-blue-100 pb-1"
                                >
                                    Session 1 (Morning)
                                </p>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p
                                            class="text-[9px] font-bold text-gray-400 uppercase mb-1"
                                        >
                                            In
                                        </p>
                                        <input
                                            type="time"
                                            v-model="shiftForm.s1_in"
                                            class="w-full p-2 bg-white border border-gray-200 rounded-lg text-sm font-bold"
                                        />
                                    </div>
                                    <div>
                                        <p
                                            class="text-[9px] font-bold text-gray-400 uppercase mb-1"
                                        >
                                            Out
                                        </p>
                                        <input
                                            type="time"
                                            v-model="shiftForm.s1_out"
                                            class="w-full p-2 bg-white border border-gray-200 rounded-lg text-sm font-bold"
                                        />
                                    </div>
                                </div>
                            </div>

                            <transition
                                enter-active-class="transition duration-200 ease-out"
                                enter-from-class="opacity-0 translate-y-2"
                                enter-to-class="opacity-100 translate-y-0"
                            >
                                <div
                                    v-if="shiftForm.session_count == 2"
                                    class="p-4 border border-gray-100 rounded-xl bg-gray-50/30"
                                >
                                    <p
                                        class="text-[11px] font-black text-[#01AAEB] uppercase mb-3 border-b border-indigo-100 pb-1"
                                    >
                                        Session 2 (Afternoon)
                                    </p>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p
                                                class="text-[9px] font-bold text-gray-400 uppercase mb-1"
                                            >
                                                In
                                            </p>
                                            <input
                                                type="time"
                                                v-model="shiftForm.s2_in"
                                                class="w-full p-2 bg-white border border-gray-200 rounded-lg text-sm font-bold"
                                            />
                                        </div>
                                        <div>
                                            <p
                                                class="text-[9px] font-bold text-gray-400 uppercase mb-1"
                                            >
                                                Out
                                            </p>
                                            <input
                                                type="time"
                                                v-model="shiftForm.s2_out"
                                                class="w-full p-2 bg-white border border-gray-200 rounded-lg text-sm font-bold"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </transition>
                        </div>
                    </div>

                    <div
                        class="mt-8 pt-6 border-t border-gray-50 flex justify-end"
                    >
                        <button
                            @click="saveShift"
                            class="px-10 py-3 bg-[#01AAEB] text-white rounded-xl font-bold uppercase text-[11px] tracking-[2px] transition active:scale-95 shadow-lg shadow-gray-200"
                        >
                            Save Shift
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
