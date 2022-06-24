import mergeFeatures from './mergeFeatures'

test('merges feature structs and arrays', () => {
  const featureStructure = {
    first: true,
    second: false,
    third: false,
    fourth: false,
  }

  const featureTwo = ['second']

  const featureThree = {
    fourth: true,
  }

  const features = mergeFeatures(featureStructure, featureTwo, featureThree)

  expect(features).toEqual(['first', 'fourth', 'second'])
})
