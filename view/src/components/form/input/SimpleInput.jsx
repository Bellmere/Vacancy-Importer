import React from "react";

const SimpleInput = ({
                         labelText,
                         name,
                         type = "text",
                         required = true,
                         value,
                         onChange,
                         placeholder,
                         error,
                         autoComplete = "off"
                     }) => {

    return (
        <label className="form-field">
            <span className="form-field__label">{labelText}</span>
                <input
                    size="40"
                    aria-required={required}
                    aria-invalid={!!error}
                    placeholder={placeholder || ""}
                    value={value}
                    type={type}
                    name={name}
                    onChange={onChange}
                    autoComplete={autoComplete}
                />
            {error && (
                <small className="red-alert text-left" style={{ marginTop: "4px" }}>
                    {error}
                </small>
            )}
        </label>
    );
};

export default SimpleInput;
