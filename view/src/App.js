import React from 'react';
import {VacancyProvider} from "./store/VacancyContext";


import VacancyListSection from "./components/sections/VacancySection/VacancyListSection";

const App = () => {
    return (
        <VacancyProvider>
            <>
                <VacancyListSection />
            </>
        </VacancyProvider>
    );
};

export default App;

