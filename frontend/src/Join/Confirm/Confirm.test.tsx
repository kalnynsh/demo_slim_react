import React from 'react'
import { createMemoryHistory } from 'history'
import { Router, MemoryRouter } from 'react-router-dom'
import { render, waitFor, screen } from '@testing-library/react'
import Confirm from './Confirm'
import api from '../../Api'

test('confirms without token', async () => {
  jest.spyOn(api, 'post')

  const history = createMemoryHistory({
    initialEntries: ['/join/confirm'],
  })

  render(
    <Router location={history.location} navigator={history}>
      <Confirm />
    </Router>
  )

  expect(history.location.pathname).toBe('/')

  expect(api.post).not.toHaveBeenCalled()
})

test('confirms successfully', async () => {
  jest.spyOn(api, 'post').mockResolvedValue(
    new Response('', {
      status: 201,
      headers: new Headers(),
    })
  )

  const history = createMemoryHistory({
    initialEntries: ['/join/confirm?token=01'],
  })

  render(
    <Router location={history.location} navigator={history}>
      <Confirm />
    </Router>
  )

  await waitFor(() => {
    expect(api.post).toHaveBeenCalled()
  })

  await waitFor(() => {
    expect(history.location.pathname).toBe('/join/success')
  })

  expect(api.post).toHaveBeenCalledWith('/v1/auth/join/confirm', {
    token: '01',
  })
})

test('shows error', async () => {
  jest.spyOn(api, 'post').mockRejectedValue(
    new Response(JSON.stringify({ message: 'Incorrect token.' }), {
      status: 409,
      headers: new Headers({ 'content-type': 'application/json' }),
    })
  )

  render(
    <MemoryRouter initialEntries={['/join/confirm?token=02']}>
      <Confirm />
    </MemoryRouter>
  )

  const alert = await screen.findByTestId('alert-error')

  expect(alert).toHaveTextContent('Incorrect token.')
})
