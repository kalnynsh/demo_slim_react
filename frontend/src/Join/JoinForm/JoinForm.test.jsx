import React from 'react'
import { render, fireEvent, screen } from '@testing-library/react'
import JoinForm from './JoinForm'

test('allows the user successfully join', async () => {
  const fetch = jest.spyOn(global, 'fetch').mockResolvedValue({
    ok: true,
    status: 201,
  })

  render(<JoinForm />)

  fireEvent.change(screen.getByLabelText('Email'), {
    target: { value: 'mail.@app.test' },
  })

  fireEvent.change(screen.getByLabelText('Password'), {
    target: { value: 'secret-Password-831' },
  })

  fireEvent.click(screen.getByLabelText(/I agree/i))

  fireEvent.click(screen.getByTestId('join-button'))

  const alert = await screen.findByTestId('alert-success')

  expect(alert).toHaveTextContent('Confirm join by link in email.')

  expect(fetch).toHaveBeenCalledWith('/api/v1/auth/join', {
    method: 'POST',
    headers: {
      Accept: 'application/join',
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      email: 'mail.@app.test',
      password: 'secret-Password-831',
    }),
  })
})

test('shows conflict error', async () => {
  jest.spyOn(global, 'fetch').mockResolvedValue({
    ok: false,
    status: 409,
    headers: new Headers({ 'Content-Type': 'application/json' }),
    json: () => Promise.resolve({ message: 'User already exists.' }),
  })

  render(<JoinForm />)

  fireEvent.change(screen.getByLabelText('Email'), {
    target: { value: 'exisating-mail@app.test' },
  })

  fireEvent.change(screen.getByLabelText('Password'), {
    target: { value: 'secret-Password-904' },
  })

  fireEvent.click(screen.getByLabelText(/I agree/i))

  fireEvent.click(screen.getByTestId('join-button'))

  const alert = await screen.findByTestId('alert-error')

  expect(alert).toHaveTextContent('User already exists.')
})

test('shows validation errors', async () => {
  jest.spyOn(global, 'fetch').mockResolvedValue({
    ok: false,
    status: 422,
    headers: new Headers({ 'Content-Type': 'application/json' }),
    json: () =>
      Promise.resolve({
        errors: {
          email: 'Incorrect email',
          password: 'Incorrect password',
          agree: 'Private policy terms do not agreed',
        },
      }),
  })

  render(<JoinForm />)

  fireEvent.change(screen.getByLabelText('Email'), {
    target: { value: 'incorrect-mail@app.test' },
  })

  fireEvent.change(screen.getByLabelText('Password'), {
    target: { value: 'pas' },
  })

  fireEvent.click(screen.getByLabelText(/I agree/i))

  fireEvent.click(screen.getByTestId('join-button'))

  const emailViolation = await screen.findByTestId('violation-email')
  expect(emailViolation).toHaveTextContent('Incorrect email')

  const passwordViolation = await screen.findByTestId('violation-password')
  expect(passwordViolation).toHaveTextContent('Incorrect password')

  const agreedViolation = await screen.findByTestId('violation-agree')

  expect(agreedViolation).toHaveTextContent(
    'Private policy terms do not agreed'
  )
})

test('shows server errors', async () => {
  jest.spyOn(global, 'fetch').mockResolvedValue({
    ok: false,
    status: 502,
    statusText: 'Bad Gateway',
    headers: new Headers(),
    test: () => Promise.resolve(''),
  })

  render(<JoinForm />)

  fireEvent.change(screen.getByLabelText('Email'), {
    target: { value: 'mail@app.test' },
  })

  fireEvent.change(screen.getByLabelText('Password'), {
    target: { value: 'passWord-7314' },
  })

  fireEvent.click(screen.getByLabelText(/I agree/i))

  fireEvent.click(screen.getByTestId('join-button'))

  const alert = await screen.findByTestId('alert-error')

  expect(alert).toHaveTextContent('Bad Gateway')
})
