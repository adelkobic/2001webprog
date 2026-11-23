function showSwaggerPage() {
    var container = document.getElementById('swagger-ui');
    if (typeof SwaggerUIBundle !== 'function') {
        container.innerHTML = '<div class="alert alert-danger">Swagger UI could not be loaded. Check your internet connection and reload this page.</div>';
        return;
    }

    SwaggerUIBundle({
        url: AppConfig.apiOrigin + '/api/openapi.yaml',
        dom_id: '#swagger-ui'
    });
}
