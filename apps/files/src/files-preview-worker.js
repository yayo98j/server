self.addEventListener('install', () => console.debug('Preview worker installed'))
self.addEventListener('activate', () => console.debug('Preview worker activated'))
self.addEventListener('message', handleMessageEvent)
self.addEventListener('fetch', handleFetchEvent)

/** @type {Object<string, string>} */
const previewAccessTokens = {}

/**
 * @param {MessageEvent} event The message event..
 * @return {void}
 */
function handleMessageEvent(event) {
	if (event.data && event.data.type === 'NEW_THUMBNAIL_ACCESS_TOKEN') {
		// eslint-disable-next-line no-console
		console.debug('Add new token for: ', event.data.fileId)
		previewAccessTokens[event.data.fileId] = event.data.token
	}
}

/**
 * @param {FetchEvent} event The fetch event.
 * @return {Promise<void>}
 */
async function handleFetchEvent(event) {
	if (event.request.url.startsWith(`${location.origin}/core/preview`)) {
		return event.respondWith(getPreview(event.request))
	}

	return event.respondWith(fetch(event.request))
}

/**
 * @param {Request} request The preview request.
 * @return {Promise<Response>}
 */
async function getPreview(request) {
	const cache = await caches.open('files-preview')
	const url = new URL(request.url)
	const fileId = url.searchParams.get('fileId') || ''
	const token = previewAccessTokens[fileId]

	// Return a cached preview if available.
	const cachedPreview = await cache.match(request.url)
	if (cachedPreview !== undefined) {
		// eslint-disable-next-line no-console
		console.debug('Return cache for: ', request.url)
		return cachedPreview
	}

	// Add the token to the headers if available.
	if (token !== undefined) {
		// Create new Request with mutable Headers.
		// mode: 'cors' is needed, else it fails silently.
		request = new Request(request, { mode: 'cors', headers: new Headers(request.headers) })
		request.headers.append('X-OC-Thumbnail-Access-Token', token)

		// eslint-disable-next-line no-console
		console.debug('Fetching preview with token for: ', request.url)
	} else {
		// eslint-disable-next-line no-console
		console.debug('Fetching preview for: ', request.url)
	}

	// Fetch and cache the preview with the token.
	const fetchedPreview = await fetch(request)
	cache.put(request.url, fetchedPreview.clone())
	return fetchedPreview
}