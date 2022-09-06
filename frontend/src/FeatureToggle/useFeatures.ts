import { useContext } from 'react'
import FeaturesContext from './FeaturesContext'

export default function useFeatures(): string[] {
  return useContext(FeaturesContext)
}
