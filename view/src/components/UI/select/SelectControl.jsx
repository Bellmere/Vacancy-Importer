import React from 'react';

const SelectControl = ({ label, options, value, onChange }) => {
    return (
        <div className="select-control">
            <select className="select-control__select" value={value} onChange={onChange}>
                {options.map(opt => (
                    <option key={opt.value} value={opt.value}>
                        {opt.label}
                    </option>
                ))}
            </select>
            {label && <label className="select-control__label">{label}</label>}
        </div>
    );
};

export default SelectControl;
