self.addEventListener('install', (event) => {

})

self.addEventListener('activate', (event) => {
})

self.addEventListener('fetch', (event) => {
	if (event.request.url.startsWith('/index.php/core/preview')) {
		event.respondWith(
			caches.match(event.request).then((resp) => {
				return resp || fetch(event.request).then((response) => {
					return caches.open('v1').then((cache) => {
						cache.put(event.request, response.clone())
						return response
					})
				})
			})
		)
	} else {
		return fetch(event.request)
	}
})
