import React, { createContext, useReducer } from 'react';
import { vacancyReducer, initialState } from './vacancyReducer';

export const VacancyContext = createContext();

export const VacancyProvider = ({ children }) => {
    const [state, dispatch] = useReducer(vacancyReducer, initialState);

    return (
        <VacancyContext.Provider value={{ state, dispatch }}>
            {children}
        </VacancyContext.Provider>
    );
};
