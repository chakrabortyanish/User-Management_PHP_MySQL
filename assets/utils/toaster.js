/* import toastr from 'toastr';
import 'toastr/build/toastr.min.css'; */
/* const toastr = require('toastr');
require('toastr/build/toastr.min.css'); */

const toastrOptions = {
    color:true,
    closeButton: true,
    progressBar: true,
    timeOut: 3000,
    positionClass: 'toast-top-right',
};

function toastSuccess(message) {
    toastr.success(message, 'Success', toastrOptions);
}

function toastError(message) {
    toastr.error(message, 'Error', toastrOptions);
}

function toastInfo(message) {
    toastr.info(message, 'Info', toastrOptions);
}

function toastWarning(message) {
    toastr.warning(message, 'Warning', toastrOptions);
}

// export { toastSuccess, toastError, toastInfo };