import axios from 'axios';
export async function isAuthenticated () {
    try {
        const access_token = localStorage.getItem('auth-token');
        
        if (access_token && access_token != 'undefined') {
            // Make a request to an authenticated route to validate the token
            const response = await axios.get('/api/user', {
                headers: {
                    'Authorization': 'Bearer ' + access_token
                }
            });
            
            // If the response is successful (status code 2xx), the token is valid
            if (access_token && response.status === 200 && response.status < 300) {
                return response;
            }
        }
        
        return false;
    } catch (error) {
        return false;
    }
}
