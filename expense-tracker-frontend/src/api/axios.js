import axios from 'axios';

const apiClient = axios.create({
    baseURL: 'https://api-saff.test/api', 
    headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`
        },
    },
});

export default apiClient;
