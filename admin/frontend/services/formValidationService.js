var ztrack = angular.module('formValidationService', []);
ztrack.service("FormValidationService", function () {

    /**
     *  Obtiene el tipo de error presente en el campo de un formulario
     *
     * @param source: El campo a evaluar
     *
     * @return String: Una cadena que contiene el mensaje de error
     */
    this.getError = function (form, field) {

        error = form[field].$error;
        var message = "";

        if (angular.isDefined(error)) {
            if (error.required) {
                message = "Este campo es requerido";
            } else if (error.email) {
                message = "Por favor ingrese un email válido";
            } else if (error.maxlength) {
                maxlength = angular.element("input[name='" + form[field].$name + "']").attr('ng-maxlength');
                message = "La longitud no puede ser mayor de " + maxlength;
            } else if (error.minlength) {
                minlength = angular.element("input[name='" + form[field].$name + "']").attr('ng-minlength');
                message = "La longitud no puede ser menor de " + minlength;
            } else if (error.number) {
                message = "Por favor ingrese un numero válido";
            } else if (error.passwordMatcher) {
                message = "Las contraseñas no coinciden";
            } else if (error.min || error.max) {
                min = angular.element("input[name='" + form[field].$name + "']").attr('min');
                max = angular.element("input[name='" + form[field].$name + "']").attr('max');
                message = "El número debe estar entre " + min + " y " + max;
            }           
            return message;
        }
    };

    /**
     *
     * @param {form} form :El formulario que contiene el campo a evaluar
     * @param {input} field :El campo a evaluar
     * @param {String} success :El nombre de la clase CSS que se aplicará si el campo es válido
     * @param {String} error :El nombre de la clase CSS que se aplicarà si el campo es inválido
     * @returns {String} :Una cadena que contiene el nombre de la clase CSS
     */
    this.validate = function (form, field, success, error) {

        if (form[field].$dirty) {
            return (form[field].$invalid) ? error : success;
        } else {
            return (form[field].$invalid && form.$submitted) ? error : null;
        }
    };

    /**
     * 
     * 
     * @returns {String} CSS class to apply
     */
    this.showIcon = function (form, field) {
        if (form[field] === undefined) {
            console.log("No existe el campo: '" + field + "' en el Formulario");
            return;
        }
        return (form[field].$dirty || form.$submitted);
    };
    
    this.showError = function (form, field) {
        return ((form[field].$invalid && form[field].$dirty) || (form[field].$invalid && form[field].$pristine && form.$submitted));
    };

    return this;
    
});
