import React, { ReactNode } from 'react'
import AuthContext from './AuthContext'

type Props = {
  isAuthenticated: boolean
  children: ReactNode
}

function DummyAuthProvider({ isAuthenticated, children }: Props) {
  const authContextValue = {
    isAuthenticated,
    getToken: () =>
      isAuthenticated ? Promise.resolve('token') : Promise.reject(new Error('Error')),
    login: () => null,
    logout: () => null,
    loading: false,
    error: null,
  }

  return <AuthContext.Provider value={authContextValue}>{children}</AuthContext.Provider>
}

export default DummyAuthProvider
