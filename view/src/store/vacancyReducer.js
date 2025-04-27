export const initialState = {
    vacancies: [],
    total: 0,
    loading: false,
    error: null,
};

export function vacancyReducer(state, action) {
    switch (action.type) {
        case 'FETCH_VACANCIES_START':
            return {
                ...state,
                loading: true,
                error: null,
            };
        case 'FETCH_VACANCIES_SUCCESS':
            return {
                ...state,
                loading: false,
                vacancies: action.payload.posts,
                total: action.payload.total
            };
        case 'FETCH_VACANCIES_ERROR':
            return {
                ...state,
                loading: false,
                error: action.payload,
            };
        default:
            return state;
    }
}
