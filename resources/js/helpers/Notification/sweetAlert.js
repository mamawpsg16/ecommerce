// useSweetAlert.js
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";

let defaultConfig = {
    timer: 2000,
    toast: true, // Use toast mode
    position: "bottom-end", // You can adjust the position as needed
    showClass: {
        popup: "swal2-noanimation",
        backdrop: "swal2-noanimation",
    },
    hideClass: {
        popup: "",
        backdrop: "",
    },
};

let confirmationConfig = {
    title: "Are you sure?",
    text: "You won't be able to revert this!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes!"
}

let successDefaultConfig = {
    icon: "success",
    title: "Successfully save!"
}

const swalSuccess = function (config = null) {
    const swal_config = config ?? successDefaultConfig;
    const configOptions = { ...defaultConfig, ...swal_config };
    console.log(configOptions);
    Swal.fire(configOptions);
};

const swalError = function (config) {
    const configOptions = { ...defaultConfig, ...config };
    Swal.fire(configOptions);
};

const swalConfirmation = function (config = null) {
    const configOptions = config ?? confirmationConfig;
    
    return new Promise((resolve) => {
        Swal.fire(configOptions).then((result) => {
            resolve(result);
        });
    });
};

const SwalDefault = Swal;

export { swalSuccess, swalError, swalConfirmation, SwalDefault };
