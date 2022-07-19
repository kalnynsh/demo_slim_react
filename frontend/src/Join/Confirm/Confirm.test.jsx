import React from 'react'
import { createMemoryHistory } from 'history'
import {
  unstable_HistoryRouter as HistoryRouter,
  MemoryRouter,
  Routes,
  Route,
} from 'react-router-dom'
import { render, screen } from '@testing-library/react'
import Confirm from './Confirm'
import api from '../../Api'

test('confirms without token', async () => {
  jest.spyOn(api, 'post')

  const history = createMemoryHistory({
    initialEntries: ['/join/confirm'],
  })

  render(
    <HistoryRouter history={history}>
      <Routes>
        <Route path="/join/confirm" element={<Confirm />} />
      </Routes>
    </HistoryRouter>
  )

  expect(history.location.pathname).toBe('/')

  expect(api.post).not.toHaveBeenCalled()
})

test('confirms successfully', async () => {
  jest.spyOn(api, 'post').mockResolvedValue({
    ok: true,
    status: 201,
    headers: new Headers(),
    text: () => Promise.resolve(''),
  })

  render(
    <MemoryRouter initialEntries={['/join/confirm?token=01']}>
      <Routes>
        <Route path="/join/confirm" element={<Confirm />} />
      </Routes>
    </MemoryRouter>
  )

  const alert = await screen.findByTestId('alert-success')

  expect(alert).toHaveTextContent('Success!')

  expect(api.post).toHaveBeenCalledWith('/v1/auth/join/confirm', {
    token: '01',
  })
})

test('shows error', async () => {
  jest.spyOn(api, 'post').mockRejectedValue({
    ok: false,
    status: 409,
    headers: new Headers({ 'content-type': 'application/json' }),
    json: () => Promise.resolve({ message: 'Incorrect token.' }),
  })

  render(
    <MemoryRouter initialEntries={['/join/confirm?token=02']}>
      <Routes>
        <Route path="/join/confirm" element={<Confirm />} />
      </Routes>
    </MemoryRouter>
  )

  const alert = await screen.findByTestId('alert-error')

  expect(alert).toHaveTextContent('Incorrect token.')
})
