import { useCallback } from "react";

const useFormValidation = () => {
    const validateForm = useCallback((formData) => {
        const errors = {};

        if (!formData.title || formData.title.trim() === "") {
            errors.title = "Vacancy title is required.";
        }

        if (!formData.description || formData.description.trim() === "") {
            errors.description = "Description is required.";
        }

        if (!formData.city || formData.city.trim() === "") {
            errors.city = "City is required.";
        }

        if (!formData.salary) {
            errors.salary = "Salary is required.";
        } else if (isNaN(formData.salary) || Number(formData.salary) <= 0) {
            errors.salary = "Salary must be a positive number.";
        }

        if (!formData.type_of_employment || formData.type_of_employment.trim() === "") {
            errors.type_of_employment = "Type of employment is required.";
        }

        return errors;
    }, []);

    return { validateForm };
};

export default useFormValidation;
