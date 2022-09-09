import parseError from './parseError'

test('response with violations', async () => {
  const response = new Response(JSON.stringify({ errors: { email: 'Wrong email' } }), {
    status: 422,
    headers: new Headers({ 'content-type': 'application/json' }),
  })

  const result = await parseError(response)
  expect(result).toBe(null)
})

test('response with error', async () => {
  const response = new Response(JSON.stringify({ message: 'Domain error' }), {
    status: 409,
    headers: new Headers({ 'content-type': 'application/json' }),
  })

  const result = await parseError(response)
  expect(result).toBe('Domain error')
})

test('html response with error', async () => {
  const response = new Response('Error', {
    status: 500,
    statusText: 'Internal server error',
    headers: new Headers({ 'content-type': 'text/plain' }),
  })

  const result = await parseError(response)
  expect(result).toBe('Internal server error')
})

test('JS error', async () => {
  const error = new Error('JS error')
  const result = await parseError(error)
  expect(result).toBe('JS error')
})
