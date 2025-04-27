export async function fetchVacancies({
                                         page = 1,
                                         perPage = 10,
                                         city = '',
                                         order = '',
                                         orderBy = ''
}) {
    const params = new URLSearchParams();

    params.append('page', page);
    params.append('per_page', perPage);

    if (city) {
        params.append('city', city);
    }

    if (order) {
        params.append('order', order);
    }

    if (orderBy) {
        params.append('order_by', orderBy);
    }

    const response = await fetch(`${wpApiSettings.root}digiway/v1/vacancies?${params.toString()}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        },
    });

    if (!response.ok) {
        const text = await response.text();
        console.error('Server response:', text);
        throw new Error('Failed to fetch vacancies');
    }

    const data = await response.json();
    return data;
}

export async function fetchCities() {
    const response = await fetch(`${wpApiSettings.root}digiway/v1/vacancies/cities`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        },
    });

    if (!response.ok) {
        const text = await response.text();
        console.error('Server response:', text);
        throw new Error('Failed to fetch cities');
    }

    const data = await response.json();
    return data;
}

export async function addVacancy(vacancy) {
    const response = await fetch(`${wpApiSettings.root}digiway/v1/vacancies`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-WP-Nonce': wpApiSettings.nonce,
        },
        body: JSON.stringify(vacancy),
    });

    const text = await response.text();

    if (!response.ok) {
        let errorMessage = 'Failed to add vacancy';

        try {
            const json = JSON.parse(text);
            if (json?.message) {
                errorMessage = json.message;
            }
        } catch (e) {
            console.error('Server response (invalid JSON):', text);
        }

        throw new Error(errorMessage);
    }

    try {
        return JSON.parse(text);
    } catch (e) {
        console.error('Failed to parse JSON:', text);
        throw new Error('Invalid server response.');
    }
}
