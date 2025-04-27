import React from 'react';

const VacancyItem = ({ vacancy, id }) => {
    return (
        <div className="vacancies-table__row">
            {vacancy.map((item, idx) => (
                <div
                    className={`vacancies-table__item ${idx === 0 ? 'sticky' : ''}`}
                    key={`${id}-${idx}`}
                >
                    {item}
                </div>
            ))}
        </div>
    );
};

export default VacancyItem;
