function Notify(type, res, msg = null) {
    var type;
    var message;

    switch (type) {
        case "error":
            message = msg ?? res.responseJSON.message ?? res.responseText ?? 'Oops! Something went wrong';
            toastr.error(message);
            break;
        case "success":
            message = msg ?? res.message ?? 'Congratulate! Operation Successful.';
            toastr.success(message);
            break;
        case "warning":
            message = msg ?? res.message ?? res.responseJSON.message ?? 'Warning! Operation Failed.';
            toastr.warning(message);
            break;
        default:
    }
}
