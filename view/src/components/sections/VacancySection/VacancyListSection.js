import React, { useEffect, useState, useContext } from 'react';
import {VacancyContext} from "../../../store/VacancyContext";
import {fetchVacancies, fetchCities} from "../../../api/vacancyApi";

//Components
import VacancyList from "../../list/VacancyList";
import SelectControl from "../../UI/select/SelectControl";
import Pagination from "../../pagination/Pagination";
import Spinner from "../../UI/Spinner";
import VacancyForm from "../../form/VacancyForm";

const headers = [
    { id: "head-vacancy", title: "Vacancy" },
    { id: "head-city", title: "City" },
    { id: "head-salary", title: "Salary" },
    { id: "head-type", title: "Type of Employment" },
    { id: "head-description", title: "Description" }
];

const VacancyListSection = () => {
    const {state, dispatch} = useContext(VacancyContext);

    const { vacancies, total, loading, error } = state;

    const [selectedCity, setSelectedCity] = useState('');
    const [sortSalary, setSortSalary] = useState('');
    const [itemsPerPage, setItemsPerPage] = useState(10);

    const [currentPage, setCurrentPage] = useState(1);
    const totalPages= Math.ceil(total / itemsPerPage);

    const [cities, setCities] = useState([]);

    useEffect(() => {
        const loadCities = async () => {
            try {
                const cityList = await fetchCities();
                setCities(cityList);
            } catch (error) {
                console.error('Failed to fetch cities', error);
            }
        };

        loadCities();
    }, []);

    useEffect(() => {
        const loadVacancies = async () => {
            dispatch({ type: 'FETCH_VACANCIES_START' });
            try {
                const data = await fetchVacancies({
                    page: currentPage,
                    perPage: itemsPerPage,
                    city: selectedCity,
                    order: sortSalary,
                    orderBy: 'salary',
                });
                dispatch({ type: 'FETCH_VACANCIES_SUCCESS', payload: data });
            } catch (err) {
                dispatch({ type: 'FETCH_VACANCIES_ERROR', payload: err.message });
            }
        };

        loadVacancies();
    }, [dispatch, currentPage, itemsPerPage, selectedCity, sortSalary]);



    return (
        <section className="vacancy-section">
            <div className="container">
                <div className="vacancy-section__controls">
                    <SelectControl
                        label="Items per page"
                        value={itemsPerPage}
                        options={[
                            { value: 10, label: '10' },
                            { value: 25, label: '25' },
                            { value: 50, label: '50' },
                        ]}
                        onChange={(e) => {
                            setItemsPerPage(Number(e.target.value));
                            setCurrentPage(1);
                        }}
                    />

                    <SelectControl
                        label="City"
                        value={selectedCity}
                        options={[
                            { value: '', label: 'All cities' },
                            ...cities.map(city =>({
                                value: city, label: city
                            }))
                        ]}
                        onChange={(e) => {
                            setSelectedCity(e.target.value);
                            setCurrentPage(1);
                        }}
                    />

                    <SelectControl
                        label="Sort by Salary"
                        value={sortSalary}
                        options={[
                            { value: 'asc', label: 'Low to High' },
                            { value: 'desc', label: 'High to Low' },
                        ]}
                        onChange={(e) => {
                            setSortSalary(e.target.value);
                            setCurrentPage(1);
                        }}
                    />
                </div>
                {loading ? (
                    <Spinner />
                ) : (
                    <>
                        <VacancyList headers={headers} vacancies={vacancies} />
                        <div className="vacancy-section__pagination">
                            <Pagination
                                currentPage={currentPage}
                                totalPages={totalPages}
                                onPageChange={(newPage) => setCurrentPage(newPage)}
                            />
                        </div>
                    </>
                )}
                <div className="vacancy-section__add-form">
                    <VacancyForm onSuccess={() => {
                        setCurrentPage(1);
                    }} />
                </div>
            </div>
        </section>
    );
}

export default VacancyListSection;
