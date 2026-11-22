var OpenLibraryService = {
    coverForIsbn: function (isbn) {
        var url = AppConfig.apiOrigin + '/api/books/cover?isbn=' + encodeURIComponent(isbn);
        return fetch(url, {
            headers: {
                'Accept': 'application/json'
            }
        }).then(function (response) {
            return response.json().then(function (data) {
                if (!response.ok) {
                    throw new Error(data.message || 'Cover lookup failed.');
                }
                return data;
            });
        });
    }
};
