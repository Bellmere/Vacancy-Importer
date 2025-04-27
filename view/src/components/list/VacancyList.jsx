import React from 'react';
import VacancyItem from "./VacancyItem";

const VacancyList = ({headers, vacancies}) => {

    return (
        <div className="vacancies-table--wrapper">
            <div className="vacancies-table__header">
                <div className="vacancies-table__row">
                    {headers.map((head, idx) => (
                        <div
                            className={`vacancies-table__item header ${idx === 0 ? 'sticky' : ''}`}
                            key={head.id}
                        >
                            {head.title}
                        </div>
                    ))}
                </div>
            </div>
            <div className="vacancies-table__body">
                {vacancies.length > 0 ? (
                    vacancies.map(vacancy => (
                        <VacancyItem
                            key={vacancy.id}
                            id={vacancy.id}
                            vacancy={[
                                vacancy.title,
                                vacancy.city,
                                vacancy.salary,
                                vacancy.type_of_employment,
                                vacancy.content
                            ]}
                        />
                    ))
                ) : (
                    <div className="error-message">No vacancies found.</div>
                )}
            </div>
        </div>
    );
};

export default VacancyList;
