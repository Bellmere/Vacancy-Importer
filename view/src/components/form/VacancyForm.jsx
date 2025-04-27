import React, { useState } from 'react';
import { addVacancy } from "../../api/vacancyApi";
import useFormValidation from "../../hook/useFormValidation";

import SimpleInput from "./input/SimpleInput";

const VacancyForm = ({ onSuccess }) => {
    const [formData, setFormData] = useState({
        title: '',
        description: '',
        city: '',
        salary: '',
        type_of_employment: '',
    });

    const [errors, setErrors] = useState({});
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);

    const { validateForm } = useFormValidation();

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData((prev) => ({ ...prev, [name]: value }));
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        const validationErrors = validateForm(formData);

        if (Object.keys(validationErrors).length > 0) {
            setErrors(validationErrors);
            return;
        }

        setLoading(true);
        setErrors({});
        setError(null);

        try {
            await addVacancy(formData);
            onSuccess();
            setFormData({
                title: '',
                description: '',
                city: '',
                salary: '',
                type_of_employment: '',
            });
        } catch (err) {
            console.error(err);
            setError(err.message || 'Failed to add vacancy.');
        } finally {
            setLoading(false);
        }
    };

    return (
        <form className="vacancy-form" onSubmit={handleSubmit}>
            <SimpleInput
                labelText="Vacancy"
                name="title"
                value={formData.title}
                onChange={handleChange}
                required
                placeholder="Enter vacancy title"
                error={errors.title}
            />
            <label className="form-field">
                <span className="form-field__label">Description</span>
                <textarea
                    name="description"
                    value={formData.description}
                    onChange={handleChange}
                    className="wpcf7-form-control wpcf7-textarea form-field__input"
                    placeholder="Enter description"
                    required
                    rows="4"
                />
            </label>
            <SimpleInput
                labelText="City"
                name="city"
                value={formData.city}
                onChange={handleChange}
                required
                placeholder="Enter city"
                error={errors.city}
            />
            <SimpleInput
                labelText="Salary"
                name="salary"
                type="number"
                value={formData.salary}
                onChange={handleChange}
                required
                placeholder="Enter salary"
                error={errors.salary}
            />
            <SimpleInput
                labelText="Type of Employment"
                name="type_of_employment"
                value={formData.type_of_employment}
                onChange={handleChange}
                required
                placeholder="Full-time, Part-time etc."
                error={errors.type_of_employment}
            />
            <div className="vacancy-form__submit">
                <button className="button" type="submit" disabled={loading}>
                    {loading ? 'Adding...' : 'Add Vacancy'}
                </button>
            </div>

            {error && <div className="error">{error}</div>}
        </form>
    );
};

export default VacancyForm;
