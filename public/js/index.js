function toFormData(object, formData = null) {
    if(!formData) {
        formData = new FormData();
    }
    for (let key in object) {
        if(Array.isArray(object[key])) {
            for(let array_item of object[key]) {
                formData.append(key, array_item);
            }
        } else {
            formData.append(key, object[key]);
        }
    }
    return formData;
}