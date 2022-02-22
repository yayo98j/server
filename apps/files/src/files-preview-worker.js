// eslint-disable-next-line no-console
console.log('HELLO FROM WEB WORKER!')

self.addEventListener('install', (event) => {

})

self.addEventListener('activate', (event) => {

})

self.addEventListener('fetch', async (event) => {
	if (!event.request.url.startsWith(`${location.origin}/core/preview`)) {
		return fetch(event.request)
	}

	return event.respondWith(getPreview(event))
})


async function getPreview(event) {
	const cachedPreview = await caches.match(event.request)

	if (cachedPreview !== undefined) {
		// eslint-disable-next-line no-console
		console.debug('Return cache for: ', event.request.url)
		return cachedPreview
	}

	// eslint-disable-next-line no-console
	console.debug('Fetching resource for: ', event.request.url)

	const headers = new Headers()
	for (const key of event.request.headers.keys()) {
		headers.append(key, event.request.headers.get(key))
	}

	headers.append('THUMBNAIL_ACCESS_TOKEN', 'random_string')

	const fetchedPreview = await fetch({
		url: event.request,
		headers
	})

	const cache = await caches.open('v1')
	cache.put(event.request, fetchedPreview.clone())

	return fetchedPreview
}